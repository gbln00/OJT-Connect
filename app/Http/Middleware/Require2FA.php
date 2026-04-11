<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Require2FA
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = auth()->user();

        if (!$user) {
            return $next($request);
        }

        // Only enforce for admin and coordinator roles
        if (!in_array($user->role, ['admin', 'ojt_coordinator'])) {
            return $next($request);
        }

        // If 2FA is enabled but not yet verified this session
        if ($user->two_factor_enabled && !session('2fa_verified')) {
            return redirect()->route('2fa.challenge');
        }

        return $next($request);
    }
}