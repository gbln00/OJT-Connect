<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\TenantUpdate;
use App\Models\SystemVersion;

/**
 * Injects $tenantInstalledVersion into every tenant layout view.
 *
 * This ensures the version number shown in the sidebar/topbar reflects
 * what the tenant has actually installed — NOT the latest system version.
 *
 * The latest system version only shows after the tenant installs the update.
 */
class TenantVersionComposer
{
    public function compose(View $view): void
    {
        $tenantId = tenancy()->tenant?->id;

        if (! $tenantId) {
            $view->with('tenantInstalledVersion', null);
            return;
        }

        // Find the latest COMPLETED (installed) update for this tenant
        $lastInstalled = TenantUpdate::where('tenant_id', $tenantId)
            ->where('status', 'completed')
            ->with('version')
            ->latest('installed_at')
            ->first();

        if ($lastInstalled && $lastInstalled->version) {
            $view->with('tenantInstalledVersion', $lastInstalled->version->version);
        } else {
            // Tenant has never installed an update — show nothing or a dash
            $view->with('tenantInstalledVersion', null);
        }
    }
}