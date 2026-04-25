<?php
namespace App\Jobs;

use App\Models\Tenant;
use App\Models\TenantNotification;
use App\Models\TenantUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InstallTenantUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;    // Don't auto-retry — let admin manually retry
    public int $timeout = 300;  // 5 minutes max

    public function __construct(
        private TenantUpdate $tenantUpdate,
        private Tenant       $tenant,
        private string       $installedBy,
    ) {}

    public function handle(): void
    {
        $version = $this->tenantUpdate->version;

        Log::info("Starting install of v{$version->version} for tenant {$this->tenant->id}");

        // ── Phase 1: Mark in progress ───────────────────────────────
        $this->tenantUpdate->update(['status' => 'in_progress']);

        try {
            // ── Phase 2: Switch to tenant context ───────────────────
            tenancy()->initialize($this->tenant);

            // ── Phase 3: Enable maintenance mode ────────────────────
            // We store a flag in cache that the middleware checks
            Cache::put("tenant_maintenance_{$this->tenant->id}", true, now()->addMinutes(10));

            // ── Phase 4: Run version-specific migrations ─────────────
            if ($version->migration_folder) {
                $migrationPath = 'database/migrations/' . $version->migration_folder;

                // Only run if the folder actually exists
                if (is_dir(base_path($migrationPath))) {
                    Artisan::call('migrate', [
                        '--path'  => $migrationPath,
                        '--force' => true,
                    ]);

                    Log::info("Migrations ran for {$migrationPath}");
                } else {
                    Log::info("No migration folder found at {$migrationPath} — skipping migrations");
                }
            }

            // Record the migration batch number
            $lastBatch =  DB::table('migrations')->max('batch') ?? 0;

            // ── Phase 5: Run optional DataPatch class ────────────────
            // Convention: app/Updates/V1_3_0/DataPatch.php
            $patchClass = $this->resolvePatchClass($version->version);
            if ($patchClass && class_exists($patchClass)) {
                Log::info("Running DataPatch: {$patchClass}");
                (new $patchClass)->run($this->tenant);
            }

            // ── Phase 6: Flush tenant cache ──────────────────────────
            Cache::flush();

            // ── Phase 7: Lift maintenance mode ───────────────────────
            Cache::forget("tenant_maintenance_{$this->tenant->id}");

            // ── Phase 8: Mark completed ──────────────────────────────
            tenancy()->end();

            $this->tenantUpdate->update([
                'status'           => 'completed',
                'installed_at'     => now(),
                'installed_by'     => $this->installedBy,
                'migration_batch'  => $lastBatch,
            ]);

            // ── Phase 9: Notify success ──────────────────────────────
            tenancy()->initialize($this->tenant);
            TenantNotification::notify(
                title:      "✅ Update v{$version->version} installed",
                message:    "The update was applied successfully. Enjoy the new features!",
                type:       'success',
                targetRole: 'admin'
            );
            tenancy()->end();

            Log::info("Install of v{$version->version} completed for tenant {$this->tenant->id}");

        } catch (\Throwable $e) {
            // ── Failure handler ──────────────────────────────────────
            Log::error("Install FAILED for tenant {$this->tenant->id} v{$version->version}: " . $e->getMessage());

            // Make sure tenancy is ended cleanly
            try { tenancy()->end(); } catch (\Throwable) {}

            // Always lift maintenance mode on failure
            Cache::forget("tenant_maintenance_{$this->tenant->id}");

            // Mark as failed with error details
            $this->tenantUpdate->update([
                'status'    => 'failed',
                'error_log' => $e->getMessage() . "\n\n" . $e->getTraceAsString(),
            ]);

            // Notify about the failure
            try {
                tenancy()->initialize($this->tenant);
                TenantNotification::notify(
                    title:      "❌ Update v{$version->version} failed",
                    message:    "Installation failed: " . $e->getMessage() . ". Please contact support.",
                    type:       'warning',
                    targetRole: 'admin'
                );
                tenancy()->end();
            } catch (\Throwable) {
                try { tenancy()->end(); } catch (\Throwable) {}
            }

            throw $e; // Re-throw so the job is marked as failed in the queue
        }
    }

    /**
     * Convert version string to DataPatch class name.
     * "1.3.0" → "App\Updates\V1_3_0\DataPatch"
     */
    private function resolvePatchClass(string $version): string
    {
        $sanitized = 'V' . str_replace('.', '_', $version);
        return "App\\Updates\\{$sanitized}\\DataPatch";
    }

    /**
     * Called if the job fails after all retries exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Job permanently failed: " . $exception->getMessage());
        Cache::forget("tenant_maintenance_{$this->tenant->id}");

        $this->tenantUpdate->update([
            'status'    => 'failed',
            'error_log' => $exception->getMessage(),
        ]);
    }
}