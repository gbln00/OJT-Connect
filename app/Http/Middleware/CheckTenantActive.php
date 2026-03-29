<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTenantActive
{
    public function handle(Request $request, Closure $next): mixed
    {
        $tenant = tenancy()->tenant;

        // If tenancy hasn't initialized yet, let the request pass
        // (PreventAccessFromCentralDomains will handle that case)
        if (! $tenant) {
            return $next($request);
        }

        $status = $tenant->status ?? 'active';

        if ($status === 'inactive') {
            abort(503, 'This institution\'s account has been disabled. Please contact support.');
        }

        return $next($request);
    }
}