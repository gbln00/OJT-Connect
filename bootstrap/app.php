<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom Authenticate middleware so /login is relative
        $middleware->redirectGuestsTo('/login');

        $middleware->alias([
            // Role-based access control middleware
            'role'               => \App\Http\Middleware\RoleMiddleware::class,

            // Register SuperAdmin middleware for central domain access control
            'super_admin'        => \App\Http\Middleware\SuperAdminMiddleware::class,

            // Block access to tenant sites when the tenant is set to inactive
            'tenant.active'      => \App\Http\Middleware\CheckTenantActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();