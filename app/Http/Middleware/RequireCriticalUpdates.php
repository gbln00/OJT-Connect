<?php
namespace App\Http\Middleware;

use App\Models\SystemVersion;
use App\Models\TenantUpdate;
use Closure;
use Illuminate\Http\Request;

class RequireCriticalUpdates
{
    // Routes that are still accessible even during a critical update block
    protected array $except = [
        'admin.whats-new.*',
        'admin.updates.*',
        'logout',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Only applies inside tenant context
        $tenant = tenancy()->tenant;
        if (! $tenant) {
            return $next($request);
        }

        // Skip for exempt routes
        foreach ($this->except as $pattern) {
            if ($request->routeIs($pattern)) {
                return $next($request);
            }
        }

        $tenantId = tenancy()->tenant?->id;
        if (! $tenantId) return $next($request);

        // Check for any critical pending update
        $hasCriticalPending = TenantUpdate::where('tenant_id', $tenantId)
            ->where('status', 'pending')
            ->whereHas('version', fn($q) => $q->where('is_critical', true)->where('is_published', true))
            ->exists();

        if ($hasCriticalPending) {
            return redirect()
                ->route('admin.whats-new.index')
                ->with('critical_update_required', true);
        }

        return $next($request);
    }
}