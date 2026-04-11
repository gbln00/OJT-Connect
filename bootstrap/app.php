<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        using: function () {
            Route::middleware('web')->group(base_path('routes/web.php'));
            require base_path('routes/tenant.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/login');

        $middleware->validateCsrfTokens(except: [
            'login',
        ]);

        // !! REMOVED: TenantSessionCookie - same problem as SessionBootstrapper,
        // it also ran before tenancy was initialized so tenant() returned null.

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