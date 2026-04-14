<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\TenantBandwidthLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AggregateTenantBandwidth extends Command
{
    protected $signature   = 'tenants:aggregate-bandwidth {--days=30 : How many days back to re-aggregate}';
    protected $description = 'Re-aggregate bandwidth from tenant_request_logs into tenant_bandwidth_logs';

    public function handle(): void
    {
        $days  = (int) $this->option('days');
        $since = now()->subDays($days)->toDateString();

        $this->info("Aggregating bandwidth for the last {$days} days...");

        // Pull aggregated bytes per tenant per day from the raw log table
        $rows = DB::connection('mysql')
            ->table('tenant_request_logs')
            ->selectRaw('tenant_id, DATE(logged_at) as date, SUM(response_size) as bytes_out, COUNT(*) as request_count')
            ->where('logged_at', '>=', $since)
            ->groupBy('tenant_id', DB::raw('DATE(logged_at)'))
            ->get();

        foreach ($rows as $row) {
            TenantBandwidthLog::updateOrCreate(
                ['tenant_id' => $row->tenant_id, 'date' => $row->date],
                [
                    'bytes_out'     => $row->bytes_out ?? 0,
                    'request_count' => $row->request_count,
                ]
            );
        }

        $this->info("Done. Processed {$rows->count()} tenant-day rows.");
    }
}