<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\InstallTenantUpdate;
use App\Models\SystemVersion;
use App\Models\TenantUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

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
    public function install(Request $request, TenantUpdate $tenantUpdate)
{
    $user = Auth::user();

    if ($user->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized.'], 403);
    }

    if (! in_array($tenantUpdate->status, ['pending', 'failed'])) {
        return response()->json(['error' => 'This update is already installed or in progress.'], 422);
    }

    $tenantUpdate->update([
        'status'    => 'in_progress',
        'error_log' => null,
    ]);

    try {
        // 1. Run tenant migrations
        Artisan::call('tenants:migrate', [
            '--tenants' => [tenancy()->tenant->id],
            '--force'   => true,
        ]);

        // 2. DO NOT call config:cache or view:cache here.
        //    These fail inside tenant context because the filesystem
        //    bootstrapper remaps storage paths.
        //    Just clear the view cache safely instead.
        Artisan::call('view:clear');

        // 3. Mark complete
        $tenantUpdate->update([
            'status'       => 'completed',
            'installed_at' => now(),
            'installed_by' => $user->email,
            'error_log'    => null,
        ]);

        Log::info("TenantUpdate #{$tenantUpdate->id} installed by {$user->email} for tenant " . tenancy()->tenant->id);

        return response()->json([
            'status'       => 'completed',
            'installed_at' => now()->format('M d, Y H:i'),
        ]);

    } catch (\Throwable $e) {
        $tenantUpdate->update([
            'status'    => 'failed',
            'error_log' => $e->getMessage() . "\n" . $e->getTraceAsString(),
        ]);

        Log::error("TenantUpdate #{$tenantUpdate->id} failed: " . $e->getMessage());

        return response()->json([
            'error' => 'Installation failed: ' . $e->getMessage(),
        ], 500);
    }
}

    /**
     * Polling endpoint — frontend calls this every 3 seconds
     * GET /admin/updates/{tenantUpdate}/status
     */
    public function status(Request $request, TenantUpdate $tenantUpdate)
    {
        return response()->json([
            'status'       => $tenantUpdate->status,
            'installed_at' => $tenantUpdate->installed_at?->format('M d, Y H:i'),
            'error_log'    => $tenantUpdate->error_log,
        ]);
    }
}