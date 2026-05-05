<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenantUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdminUpdateController extends Controller
{
    /**
     * Trigger the installation of a pending update.
     * Runs synchronously (no queue) since the free Render tier
     * has a single container — queue worker + web share the same process.
     */
    public function install(Request $request, TenantUpdate $tenantUpdate)
    {
        $user = Auth::user();

        // Only the tenant admin can install updates
        if ($user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        // Only pending or failed updates can be installed
        if (! in_array($tenantUpdate->status, ['pending', 'failed'])) {
            return response()->json(['error' => 'This update is already installed or in progress.'], 422);
        }

        // Mark as in_progress immediately so the UI shows the spinner
        $tenantUpdate->update([
            'status'     => 'in_progress',
            'error_log'  => null,
        ]);

        try {
            // ── Run the actual install steps ──────────────────────────

            // 1. Re-run tenant migrations in case the update includes schema changes
            Artisan::call('tenants:migrate', [
                '--tenants' => [tenancy()->tenant->id],
                '--force'   => true,
            ]);

            // 2. Clear and rebuild config/view cache
            //    IMPORTANT: We do NOT call config:clear or cache:clear here
            //    because those wipe the file session store and log everyone out.
            //    Instead we just rebuild — new cache overwrites old in place.
            Artisan::call('config:cache');
            Artisan::call('view:cache');

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
     * Poll endpoint — the frontend calls this every 3 seconds
     * to check if an in-progress install is done.
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