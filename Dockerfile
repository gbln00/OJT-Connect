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
    pdo pdo_mysql mbstring xml curl zip gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Remove local .env so Render ENV variables are used
RUN rm -f .env bootstrap/cache/*.php

RUN composer install --optimize-autoloader --no-scripts --no-interaction
RUN npm ci && npm run build

RUN mkdir -p storage/framework/{sessions,views,cache,testing} \
    storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html

RUN echo 'display_errors = On' > /usr/local/etc/php/conf.d/errors.ini \
    && echo 'error_log = /dev/stderr' >> /usr/local/etc/php/conf.d/errors.ini

RUN echo 'server { \
    listen 8080; \
    root /var/www/html/public; \
    index index.php; \
    location / { \
        try_files $uri $uri/ /index.php?$query_string; \
    } \
    location ~ \.php$ { \
        fastcgi_pass 127.0.0.1:9000; \
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name; \
        include fastcgi_params; \
    } \
}' > /etc/nginx/http.d/default.conf

RUN echo '#!/bin/sh' > /start.sh \
    && echo 'cd /var/www/html' >> /start.sh \
    && echo 'php artisan migrate --force 2>&1' >> /start.sh \
    && echo 'php artisan storage:link --force 2>&1' >> /start.sh \
    && echo 'php artisan view:clear 2>&1' >> /start.sh \
    && echo 'php artisan route:clear 2>&1' >> /start.sh \
    && echo 'php artisan config:clear 2>&1' >> /start.sh \
    && echo 'touch storage/logs/laravel.log' >> /start.sh \
    && echo 'chmod 777 storage/logs/laravel.log' >> /start.sh \
    && echo 'php-fpm -D' >> /start.sh \
    && echo 'tail -f storage/logs/laravel.log &' >> /start.sh \
    && echo 'nginx -g "daemon off;"' >> /start.sh \
    && chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]