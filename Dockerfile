FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    git curl zip unzip \
    libzip-dev \
    libpng-dev \
    libxml2-dev \
    oniguruma-dev \
    curl-dev \
    nodejs npm \
    nginx

RUN docker-php-ext-install \
    pdo pdo_mysql mbstring xml curl zip gd opcache

# ── OPcache (biggest PHP speed win) ──────────────────────────────
RUN echo 'opcache.enable=1' > /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.enable_cli=0' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.memory_consumption=128' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.interned_strings_buffer=16' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.max_accelerated_files=10000' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.revalidate_freq=0' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.validate_timestamps=0' >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo 'opcache.fast_shutdown=1' >> /usr/local/etc/php/conf.d/opcache.ini

# ── PHP-FPM tuning ────────────────────────────────────────────────
RUN echo '[www]' > /usr/local/etc/php-fpm.d/zz-tuning.conf \
    && echo 'pm = dynamic' >> /usr/local/etc/php-fpm.d/zz-tuning.conf \
    && echo 'pm.max_children = 10' >> /usr/local/etc/php-fpm.d/zz-tuning.conf \
    && echo 'pm.start_servers = 2' >> /usr/local/etc/php-fpm.d/zz-tuning.conf \
    && echo 'pm.min_spare_servers = 1' >> /usr/local/etc/php-fpm.d/zz-tuning.conf \
    && echo 'pm.max_spare_servers = 4' >> /usr/local/etc/php-fpm.d/zz-tuning.conf \
    && echo 'pm.max_requests = 500' >> /usr/local/etc/php-fpm.d/zz-tuning.conf \
    && echo 'request_terminate_timeout = 60' >> /usr/local/etc/php-fpm.d/zz-tuning.conf

# ── PHP error display ─────────────────────────────────────────────
RUN echo 'display_errors = Off' > /usr/local/etc/php/conf.d/errors.ini \
    && echo 'error_log = /dev/stderr' >> /usr/local/etc/php/conf.d/errors.ini \
    && echo 'log_errors = On' >> /usr/local/etc/php/conf.d/errors.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction
RUN npm ci && npm run build

RUN mkdir -p storage/framework/{sessions,views,cache,testing} \
    storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html

# ── Nginx with gzip + keep-alive ──────────────────────────────────
RUN printf 'server {\n\
    listen 8080;\n\
    root /var/www/html/public;\n\
    index index.php;\n\
    client_max_body_size 20M;\n\
\n\
    # Gzip compression\n\
    gzip on;\n\
    gzip_vary on;\n\
    gzip_proxied any;\n\
    gzip_comp_level 5;\n\
    gzip_min_length 256;\n\
    gzip_types\n\
        text/plain text/css text/xml text/javascript\n\
        application/json application/javascript application/xml\n\
        application/rss+xml image/svg+xml;\n\
\n\
    # Static asset caching\n\
    location ~* \\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {\n\
        expires 30d;\n\
        add_header Cache-Control "public, immutable";\n\
        try_files $uri =404;\n\
    }\n\
\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
\n\
    location ~ \\.php$ {\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;\n\
        fastcgi_read_timeout 60;\n\
        fastcgi_send_timeout 60;\n\
        fastcgi_buffers 16 16k;\n\
        fastcgi_buffer_size 32k;\n\
        include fastcgi_params;\n\
    }\n\
}\n' > /etc/nginx/http.d/default.conf

# ── start.sh ──────────────────────────────────────────────────────
RUN printf '#!/bin/sh\n\
set -e\n\
cd /var/www/html\n\
\n\
echo "==> Running migrations..."\n\
php artisan migrate --force 2>&1\n\
\n\
echo "==> Running tenant migrations..."\n\
php artisan tenants:migrate --force 2>&1\n\
\n\
echo "==> Linking storage..."\n\
php artisan storage:link --force 2>&1\n\
\n\
echo "==> Building caches..."\n\
php artisan config:cache 2>&1\n\
php artisan view:cache 2>&1\n\
\n\
echo "==> Seeding..."\n\
php artisan db:seed --class=SuperAdminSeeder --force 2>&1 || true\n\
php artisan db:seed --class=PlanSeeder --force 2>&1 || true\n\
php artisan db:seed --class=DemoTenantSeeder --force 2>&1 || true\n\
\n\
touch storage/logs/laravel.log\n\
chmod 777 storage/logs/laravel.log\n\
\n\
echo "==> Starting PHP-FPM..."\n\
php-fpm -D\n\
\n\
echo "==> Starting Nginx..."\n\
nginx -g "daemon off;" &\n\
\n\
echo "==> Starting queue worker..."\n\
php artisan queue:work database --sleep=5 --tries=1 --timeout=120 --max-jobs=50 --verbose 2>&1\n\
' > /start.sh && chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]