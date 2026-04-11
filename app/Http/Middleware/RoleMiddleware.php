<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        \Log::info('=== ROLE MIDDLEWARE ===', [
            'url'          => $request->fullUrl(),
            'auth_check'   => Auth::check(),
            'user_id'      => Auth::id(),
            'user_role'    => Auth::user()?->role,
            'allowed'      => $roles,
            'session_id'   => session()->getId(),
            'session_cookie' => config('session.cookie'),
            'session_has_user' => session()->has('login_web_' . sha1(\Illuminate\Auth\SessionGuard::class)),
        ]);

        if (!Auth::check()) {
            \Log::warning('=== ROLE MIDDLEWARE: NOT AUTHENTICATED -> redirect login ===');
            return redirect('/login');
        }

        if (!in_array(Auth::user()->role, $roles)) {
            \Log::warning('=== ROLE MIDDLEWARE: WRONG ROLE ===', [
                'user_role'    => Auth::user()->role,
                'allowed'      => $roles,
            ]);
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}