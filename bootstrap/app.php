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
        $middleware->redirectGuestsTo('/login');

        $middleware->alias([
            // Custom middleware aliases
            'role'          => \App\Http\Middleware\RoleMiddleware::class,
            'super_admin'   => \App\Http\Middleware\SuperAdminMiddleware::class,
            'tenant.active' => \App\Http\Middleware\CheckTenantActive::class,
            'plan'          => \App\Http\Middleware\CheckTenantPlan::class,
        ]);

        // Global middleware for tenant request logging
        $middleware->web(append: [
            \App\Http\Middleware\LogTenantRequest::class,
        ]);
    })

   

    

    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

    