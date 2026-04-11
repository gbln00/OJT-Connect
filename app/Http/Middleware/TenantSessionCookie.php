<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantSessionCookie
{
    public function handle(Request $request, Closure $next)
    {
        if (tenancy()->initialized) {
            config([
                'session.cookie'     => 'tenant_' . tenant('id') . '_session',
                'session.connection' => null, 
            ]);
        }

        return $next($request);
    }
}