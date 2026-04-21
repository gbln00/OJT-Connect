<?php
namespace App\Http\Middleware;

use App\Models\TenantUpdate;
use Closure;
use Illuminate\Http\Request;

class RequireCriticalUpdates
{
    protected array $except = [
        'admin.whats-new.*',
        'admin.updates.*',
        'logout',
        'central.logout',
        '2fa.*',
        'password.*',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Only applies inside an initialized tenant context
        if (! tenancy()->initialized) {
            return $next($request);
        }

        $tenant = tenancy()->tenant;
        if (! $tenant) {
            return $next($request);
        }

        // Skip exempt routes
        foreach ($this->except as $pattern) {
            if ($request->routeIs($pattern)) {
                return $next($request);
            }
        }

        // Check for any critical pending update for this tenant
        $hasCriticalPending = TenantUpdate::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->whereHas('version', fn($q) =>
                $q->where('is_critical', true)
                  ->where('is_published', true)
            )
            ->exists();

        if ($hasCriticalPending) {
            return redirect()
                ->route('admin.whats-new.index')
                ->with('critical_update_required', true);
        }

        return $next($request);
    }
}