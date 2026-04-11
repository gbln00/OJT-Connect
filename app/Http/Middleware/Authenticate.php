<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        parent::authenticate($request, $guards);
    }

    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : '/login';
    }
}