<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Check if the user's role is in the allowed roles
        if (!in_array(Auth::user()->role, $roles)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Redirect to login with an error message
            return redirect('/login')->withErrors([ 
                'email' => 'You do not have access to that page.',
            ]);
        }

        return $next($request);
    }
}