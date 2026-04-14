<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\Tenant;
use App\Models\TenantBandwidthLog;
use App\Models\TenantRequestLog;
use Illuminate\Support\Facades\DB;

class TenantMetricsService
{
    /**
     * Full metrics for a single tenant (used in monitoring/show).
     */
    public function getMetrics(Tenant $tenant): array
    {
        $dbSizeMb     = $this->getDbSizeMb($tenant);
        $storageMb    = $this->getStorageMb($tenant);
        $requests7d   = TenantRequestLog::recentCountFor($tenant->id, 7);
        $requests30d  = TenantRequestLog::recentCountFor($tenant->id, 30);
        $lastActivity = TenantRequestLog::lastActivityFor($tenant->id);
        $capUsage     = $this->getCapUsage($tenant);
        $recordCounts = $this->getRecordCounts($tenant);

        $bw7d    = TenantBandwidthLog::totalBytesFor($tenant->id, 7);
        $bw30d   = TenantBandwidthLog::totalBytesFor($tenant->id, 30);
        $bwDaily = TenantBandwidthLog::dailyFor($tenant->id, 30);

        return [
            'db_size_mb'    => $dbSizeMb,
            'storage_mb'    => $storageMb,
            'requests_7d'   => $requests7d,
            'requests_30d'  => $requests30d,
            'last_activity' => $lastActivity,
            'cap_usage'     => $capUsage,
            'record_counts' => $recordCounts,

            'bandwidth' => [
                'bytes_in_7d'   => $bw7d['bytes_in'],
                'bytes_out_7d'  => $bw7d['bytes_out'],
                'bytes_in_30d'  => $bw30d['bytes_in'],
                'bytes_out_30d' => $bw30d['bytes_out'],
                'mb_in_7d'      => TenantBandwidthLog::toMb($bw7d['bytes_in']),
                'mb_out_7d'     => TenantBandwidthLog::toMb($bw7d['bytes_out']),
                'mb_in_30d'     => TenantBandwidthLog::toMb($bw30d['bytes_in']),
                'mb_out_30d'    => TenantBandwidthLog::toMb($bw30d['bytes_out']),
                'total_mb_30d'  => TenantBandwidthLog::toMb($bw30d['bytes_in'] + $bw30d['bytes_out']),
                'daily'         => $bwDaily,
            ],
        ];
    }

    /**
     * Lightweight summaries for all tenants (used in monitoring/index).
     */
    public function getAllSummaries(): array
    {
        $tenants   = Tenant::all();
        $summaries = [];

        foreach ($tenants as $tenant) {
            $bw30d = TenantBandwidthLog::totalBytesFor($tenant->id, 30);

            $summaries[$tenant->id] = [
                'db_size_mb'       => $this->getDbSizeMb($tenant),
                'storage_mb'       => $this->getStorageMb($tenant),
                'requests_7d'      => TenantRequestLog::recentCountFor($tenant->id, 7),
                'last_activity'    => TenantRequestLog::lastActivityFor($tenant->id),
                'bandwidth_mb_30d' => TenantBandwidthLog::toMb($bw30d['bytes_in'] + $bw30d['bytes_out']),
                'bytes_in_30d'     => $bw30d['bytes_in'],
                'bytes_out_30d'    => $bw30d['bytes_out'],
            ];
        }

        return $summaries;
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Total size of the tenant's dedicated MySQL database in MB.
     * Database name prefix is "tenant" per config/tenancy.php database.prefix.
     */
    private function getDbSizeMb(Tenant $tenant): float
    {
        $dbName = 'tenant' . $tenant->id;

        $result = DB::connection('mysql')->selectOne(
            "SELECT ROUND(SUM(data_length + index_length) / 1048576, 2) AS size_mb
             FROM information_schema.TABLES
             WHERE table_schema = ?",
            [$dbName]
        );

        return (float) ($result->size_mb ?? 0);
    }

    /**
     * Total size of the tenant's storage folder in MB.
     * Resolves to storage/app/tenantXXX/ per FilesystemTenancyBootstrapper defaults.
     */
    private function getStorageMb(Tenant $tenant): float
    {
        $path = storage_path('app/tenant' . $tenant->id);

        if (!is_dir($path)) {
            return 0.0;
        }

        $bytes = 0;

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $bytes += $file->getSize();
                }
            }
        } catch (\Throwable) {
            return 0.0;
        }

        return round($bytes / 1048576, 2);
    }

    /**
     * Student cap usage for the tenant's current plan.
     * Counts active student_intern users inside the tenant's own database.
     */
    private function getCapUsage(Tenant $tenant): array
    {
        $plan = Plan::where('name', $tenant->plan ?? 'basic')->first();
        $cap  = $plan?->student_cap; // null = unlimited (Premium)

        $used = 0;
        try {
            $used = $tenant->run(function () {
                return DB::table('users')
                    ->where('role', 'student_intern')
                    ->where('is_active', true)
                    ->count();
            });
        } catch (\Throwable) {
            $used = 0;
        }

        $percent = ($cap && $cap > 0)
            ? min(100, round(($used / $cap) * 100))
            : null;

        return [
            'used'    => $used,
            'cap'     => $cap,
            'percent' => $percent,
        ];
    }

    /**
     * Row counts for key tables inside the tenant's database.
     * Matches the labels shown in monitoring/show.blade.php.
     */
    private function getRecordCounts(Tenant $tenant): array
    {
        $defaults = [
            'students'       => 0,
            'applications'   => 0,
            'hour_logs'      => 0,
            'weekly_reports' => 0,
            'evaluations'    => 0,
        ];

        try {
            return $tenant->run(function () {
                return [
                    'students'       => DB::table('users')->where('role', 'student_intern')->count(),
                    'applications'   => DB::table('applications')->count(),
                    'hour_logs'      => DB::table('hour_logs')->count(),
                    'weekly_reports' => DB::table('weekly_reports')->count(),
                    'evaluations'    => DB::table('evaluations')->count(),
                ];
            });
        } catch (\Throwable) {
            return $defaults;
        }
    }
}