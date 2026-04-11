<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        \Log::info('=== AUTHENTICATE MIDDLEWARE ===', [
            'url'            => $request->fullUrl(),
            'session_id'     => session()->getId(),
            'session_cookie' => config('session.cookie'),
            'session_driver' => config('session.driver'),
            'auth_check'     => auth()->check(),
            'user_id'        => auth()->id(),
            'is_tenant'      => app(\Stancl\Tenancy\Tenancy::class)->initialized,
            'tenant_id'      => app(\Stancl\Tenancy\Tenancy::class)->initialized ? tenant('id') : null,
        ]);

        parent::authenticate($request, $guards);
    }

    protected function redirectTo(Request $request): ?string
    {
        \Log::warning('=== AUTHENTICATE: UNAUTHENTICATED - redirecting to /login ===', [
            'url'        => $request->fullUrl(),
            'session_id' => session()->getId(),
        ]);

        return $request->expectsJson() ? null : '/login';
    }
}