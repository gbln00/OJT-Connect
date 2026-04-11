<?php

namespace App\TenancyBootstrappers;

use Illuminate\Support\Str;
use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class SessionBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant): void
    {
        // Give each tenant its own session cookie name so auth sessions
        // never bleed across subdomains.
        //
        // 'connection' must point to the CENTRAL database (where the
        // sessions table lives). It must NOT switch to the tenant DB.
        // Your central connection in database.php is named 'mysql'.
        config([
            'session.cookie'     => 'tenant_' . $tenant->getTenantKey() . '_session',
            'session.connection' => 'mysql',
        ]);
    }

    public function revert(): void
    {
        config([
            'session.cookie'     => Str::slug(config('app.name', 'laravel')) . '_session',
            'session.connection' => 'mysql',
        ]);
    }
}