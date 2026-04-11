<?php

declare(strict_types=1);

use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

return [
    'tenant_model' => \App\Models\Tenant::class,
    'id_generator' => Stancl\Tenancy\UUIDGenerator::class,

    'domain_model' => Domain::class,

    'central_domains' => [
        'localhost',
        '127.0.0.1',
    ],

    'bootstrappers' => [
        Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
        Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,

        // !! REMOVED: App\TenancyBootstrappers\SessionBootstrapper::class !!
        //
        // ROOT CAUSE OF LOGIN LOOP:
        // Laravel's web middleware group runs StartSession BEFORE route
        // middleware runs InitializeTenancyByDomain. So SessionBootstrapper
        // always fires AFTER StartSession has already opened the session
        // using the default cookie name (e.g. "ojtconnect-session").
        //
        // SessionBootstrapper then renames config('session.cookie') to
        // "tenant_X_session", which only affects the Set-Cookie header on
        // the response — not the already-open session object.
        //
        // On the next request the browser sends "tenant_X_session", but
        // StartSession reads "ojtconnect-session" again → finds nothing →
        // empty session → user appears unauthenticated → redirect to /login
        // → infinite loop.
        //
        // The fix: stop renaming the cookie. Use one cookie name for all
        // tenants. Session DATA isolation is guaranteed by the tenant DB
        // (DatabaseTenancyBootstrapper switches the DB connection), not by
        // the cookie name. SESSION_DOMAIN=.localhost already ensures the
        // cookie is sent to all subdomains.
    ],

    'database' => [
        'central_connection' => env('DB_CONNECTION', 'mysql'),
        'template_tenant_connection' => null,
        'prefix' => 'tenant',
        'suffix' => '',
        'managers' => [
            'sqlite' => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
            'mysql'  => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
            'mariadb'=> Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
            'pgsql'  => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,
        ],
    ],

    'cache' => [
        'tag_base' => 'tenant',
    ],

    'filesystem' => [
        'suffix_base' => 'tenant',
        'disks' => [
            'local',
            'public',
        ],
        'root_override' => [
            'local'  => '%storage_path%/app/',
            'public' => '%storage_path%/app/public/',
        ],
        'suffix_storage_path' => true,
        'asset_helper_tenancy' => true,
    ],

    'redis' => [
        'prefix_base' => 'tenant',
        'prefixed_connections' => [],
    ],

    'features' => [],

    'routes' => true,

    'migration_parameters' => [
        '--force' => true,
        '--path' => [database_path('migrations/tenant')],
        '--realpath' => true,
    ],

    'seeder_parameters' => [
        '--class' => 'DatabaseSeeder',
    ],
];