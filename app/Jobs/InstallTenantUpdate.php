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
    
        $this->tenantUpdate->update(['status' => 'in_progress']);
    
        try {
            // ── Clear view cache BEFORE switching to tenant context ──────
            // The FilesystemTenancyBootstrapper changes storage paths which
            // breaks the compiled view path that was cached on startup.
            Artisan::call('view:clear');
            Log::info("View cache cleared");
    
            tenancy()->initialize($this->tenant);
    
            // ── Run version-specific migrations ──────────────────────────
            if ($version->migration_folder) {
                $migrationPath = 'database/migrations/' . $version->migration_folder;
    
                if (is_dir(base_path($migrationPath))) {
                    Artisan::call('migrate', [
                        '--path'  => $migrationPath,
                        '--force' => true,
                    ]);
                    Log::info("Migrations ran for {$migrationPath}");
                } else {
                    Log::info("No migration folder at {$migrationPath} — skipping");
                }
            }
    
            $lastBatch = DB::table('migrations')->max('batch') ?? 0;
    
            // ── Run optional DataPatch ────────────────────────────────────
            $patchClass = $this->resolvePatchClass($version->version);
            if ($patchClass && class_exists($patchClass)) {
                Log::info("Running DataPatch: {$patchClass}");
                (new $patchClass)->run($this->tenant);
            }
    
            tenancy()->end();
    
            // ── Rebuild view cache after tenancy ended ───────────────────
            Artisan::call('view:cache');
            Log::info("View cache rebuilt");
    
            // ── Mark completed ────────────────────────────────────────────
            $this->tenantUpdate->update([
                'status'          => 'completed',
                'installed_at'    => now(),
                'installed_by'    => $this->installedBy,
                'migration_batch' => $lastBatch,
            ]);
    
            // ── Notify success ────────────────────────────────────────────
            tenancy()->initialize($this->tenant);
            TenantNotification::notify(
                title:      "✅ Update v{$version->version} installed",
                message:    "The update was applied successfully.",
                type:       'success',
                targetRole: 'admin'
            );
            tenancy()->end();
    
            Log::info("Install of v{$version->version} completed for tenant {$this->tenant->id}");
    
        } catch (\Throwable $e) {
            Log::error("Install FAILED for tenant {$this->tenant->id} v{$version->version}: " . $e->getMessage());
    
            try { tenancy()->end(); } catch (\Throwable) {}
    
            // Rebuild view cache even on failure
            try { Artisan::call('view:clear'); } catch (\Throwable) {}
    
            $this->tenantUpdate->update([
                'status'    => 'failed',
                'error_log' => $e->getMessage() . "\n\n" . $e->getTraceAsString(),
            ]);
    
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
    
            throw $e;
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

        $this->tenantUpdate->update([
            'status'    => 'failed',
            'error_log' => $exception->getMessage(),
        ]);
    }
    
}