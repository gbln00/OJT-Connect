<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTenantPlan
{
    // Hierarchy: basic < standard < premium
    private const LEVELS = ['basic' => 1, 'standard' => 2, 'premium' => 3];

    public function handle(Request $request, Closure $next, string $required): mixed
    {
        $tenant = tenancy()->tenant;

        if (!$tenant) return $next($request);

        $current  = strtolower($tenant->plan ?? 'basic');
        $required = strtolower($required);

        $currentLevel  = self::LEVELS[$current]  ?? 0;
        $requiredLevel = self::LEVELS[$required] ?? 99;

        if ($currentLevel < $requiredLevel) {
            // Return a branded "upgrade required" response
            return response()->view('errors.plan-required', [
                'required' => ucfirst($required),
                'current'  => ucfirst($current),
            ], 403);
        }

        return $next($request);
    }
}