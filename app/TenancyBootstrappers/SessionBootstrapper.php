<?php

namespace App\TenancyBootstrappers;


use Stancl\Tenancy\Contracts\TenancyBootstrapper;
use Stancl\Tenancy\Contracts\Tenant;

class SessionBootstrapper implements TenancyBootstrapper
{
    public function bootstrap(Tenant $tenant): void
    {
        config([
            'session.cookie'     => 'tenant_' . $tenant->getTenantKey() . '_session',
            'session.connection' => null,
        ]);
    }

    public function revert(): void
    {
        config([
            'session.cookie'     => \Illuminate\Support\Str::slug(config('app.name', 'laravel')) . '_session',
            'session.connection' => 'central',
        ]);
    }
}