<?php
namespace App\Services;
 
use App\Models\Tenant;
use App\Models\TenantRequestLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
 
class TenantMetricsService
{
    /**
     * Get all metrics for a single tenant.
     * Returns an array safe to pass directly to Blade.
     */
    public function getMetrics(Tenant $tenant): array
    {
        $dbName = $this->getTenantDatabaseName($tenant);
 
        return [
            'db_size_mb'       => $this->getDatabaseSizeMb($dbName),
            'storage_mb'       => $this->getStorageMb($tenant->id),
            'record_counts'    => $this->getRecordCounts($tenant),
            'cap_usage'        => $this->getCapUsage($tenant),
            'last_activity'    => TenantRequestLog::lastActivityFor($tenant->id),
            'requests_7d'      => TenantRequestLog::recentCountFor($tenant->id, 7),
            'requests_30d'     => TenantRequestLog::recentCountFor($tenant->id, 30),
        ];
    }
 
    /**
     * Get lightweight summary metrics for ALL tenants (used on the index list).
     */
    public function getAllSummaries(): array
    {
        $tenants = Tenant::with('domains')->get();
        $summaries = [];
 
        foreach ($tenants as $tenant) {
            $dbName = $this->getTenantDatabaseName($tenant);
            $summaries[$tenant->id] = [
                'db_size_mb'    => $this->getDatabaseSizeMb($dbName),
                'storage_mb'    => $this->getStorageMb($tenant->id),
                'requests_7d'   => TenantRequestLog::recentCountFor($tenant->id, 7),
                'last_activity' => TenantRequestLog::lastActivityFor($tenant->id)?->logged_at,
            ];
        }
 
        return $summaries;
    }
 
    // ── Private Helpers ──────────────────────────────────────────
 
    private function getTenantDatabaseName(Tenant $tenant): string
    {
        // Stancl default: tenant_{id}
        return 'tenant_' . $tenant->id;
    }
 
    private function getDatabaseSizeMb(string $dbName): float
    {
        try {
            $result = DB::select("
                SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables
                WHERE table_schema = ?
            ", [$dbName]);
 
            return (float) ($result[0]->size_mb ?? 0);
        } catch (\Throwable) {
            return 0.0;
        }
    }
 
    private function getStorageMb(string $tenantId): float
    {
        try {
            // Adjust the path prefix to match your tenant storage structure
            $disk  = Storage::disk('public');
            $files = $disk->allFiles('tenants/' . $tenantId);
            $bytes = array_sum(array_map(fn($f) => $disk->size($f), $files));
            return round($bytes / 1024 / 1024, 2);
        } catch (\Throwable) {
            return 0.0;
        }
    }
 
    private function getRecordCounts(Tenant $tenant): array
    {
        try {
            return $tenant->run(function () {
                return [
                    'students'      => DB::table('users')->where('role', 'student')->count(),
                    'applications'  => DB::table('applications')->count(),
                    'hour_logs'     => DB::table('hour_logs')->count(),
                    'weekly_reports'=> DB::table('weekly_reports')->count(),
                    'evaluations'   => DB::table('evaluations')->count(),
                ];
            });
        } catch (\Throwable) {
            return [
                'students' => 0, 'applications' => 0,
                'hour_logs' => 0, 'weekly_reports' => 0, 'evaluations' => 0,
            ];
        }
    }
 
    private function getCapUsage(Tenant $tenant): array
    {
        $plan = \App\Models\Plan::where('name', $tenant->plan ?? 'basic')->first();
        $cap  = $plan?->student_cap;
 
        try {
            $used = $tenant->run(function () {
                return DB::table('applications')
                    ->whereIn('status', ['approved', 'pending'])
                    ->count();
            });
        } catch (\Throwable) {
            $used = 0;
        }
 
        return [
            'used'      => $used,
            'cap'       => $cap,          // null = unlimited (Premium)
            'percent'   => $cap ? min(100, round(($used / $cap) * 100)) : null,
        ];
    }
}
