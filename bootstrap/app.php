<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function () {
            require __DIR__.'/../routes/web.php';
            require __DIR__.'/../routes/tenant.php';
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/login');

        $middleware->validateCsrfTokens(except: [
            'login',
        ]);

        $middleware->prependToGroup('web', \App\Http\Middleware\TenantSessionCookie::class);

        $middleware->alias([
            'role'          => \App\Http\Middleware\RoleMiddleware::class,
            'super_admin'   => \App\Http\Middleware\SuperAdminMiddleware::class,
            'tenant.active' => \App\Http\Middleware\CheckTenantActive::class,
            'plan'          => \App\Http\Middleware\CheckTenantPlan::class,
            '2fa'           => \App\Http\Middleware\Require2FA::class,
        ]);

        $middleware->api(prepend: [
            \Illuminate\Http\Middleware\HandleCors::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\LogTenantRequest::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();