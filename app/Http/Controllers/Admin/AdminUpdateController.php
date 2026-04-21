<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\InstallTenantUpdate;
use App\Models\SystemVersion;
use App\Models\TenantUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUpdateController extends Controller
{
    /**
     * Called from What's New page — shows the install button state
     * Returns all TenantUpdate records for this tenant
     */
    public function pendingUpdates()
    {
        $tenantId = tenancy()->tenant?->id;

        $updates = TenantUpdate::with('version')
            ->where('tenant_id', $tenantId)
            ->orderByDesc('id')
            ->get();

        return $updates;
    }

    /**
     * Tenant admin triggers an install
     * POST /admin/updates/{tenantUpdate}/install
     */
    public function install(TenantUpdate $tenantUpdate)
    {
        $tenantId = tenancy()->tenant?->id;

        // Security: make sure this record belongs to this tenant
        abort_if($tenantUpdate->tenant_id !== $tenantId, 403);

        // Can't re-install if already done or running
        if (! $tenantUpdate->isPending() && ! $tenantUpdate->isFailed()) {
            return response()->json([
                'error' => 'This update is already ' . $tenantUpdate->status,
            ], 422);
        }

        // Enforce update order: check if required version is installed
        $version         = $tenantUpdate->version;
        $requiredVersion = $version->requiredVersion();

        if ($requiredVersion) {
            $prerequisite = TenantUpdate::where('tenant_id', $tenantId)
                ->where('version_id', $requiredVersion->id)
                ->first();

            if (! $prerequisite || ! $prerequisite->isCompleted()) {
                return response()->json([
                    'error'    => "You must install v{$requiredVersion->version} first.",
                    'requires' => $requiredVersion->version,
                ], 422);
            }
        }

        // Check no other update is already running for this tenant
        $inProgress = TenantUpdate::where('tenant_id', $tenantId)
            ->where('status', 'in_progress')
            ->exists();

        if ($inProgress) {
            return response()->json([
                'error' => 'Another update is already in progress. Please wait.',
            ], 422);
        }

        // Dispatch the background job
        InstallTenantUpdate::dispatch(
            $tenantUpdate,
            tenancy()->tenant,
            Auth::user()->email
        );

        return response()->json([
            'ok'      => true,
            'message' => "Installation of v{$version->version} started.",
            'status'  => 'in_progress',
        ]);
    }

    /**
     * Polling endpoint — frontend calls this every 3 seconds
     * GET /admin/updates/{tenantUpdate}/status
     */
    public function status(TenantUpdate $tenantUpdate)
    {
        $tenantId = tenancy()->tenant?->id;
        abort_if($tenantUpdate->tenant_id !== $tenantId, 403);

        // Refresh from DB
        $tenantUpdate->refresh();

        return response()->json([
            'status'       => $tenantUpdate->status,
            'installed_at' => $tenantUpdate->installed_at?->format('M d, Y H:i'),
            'error_log'    => $tenantUpdate->isFailed() ? $tenantUpdate->error_log : null,
        ]);
    }
}