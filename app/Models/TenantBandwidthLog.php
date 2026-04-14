<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TenantBandwidthLog extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'tenant_id', 'date', 'bytes_in', 'bytes_out', 'request_count',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Total bytes for a tenant in the last N days
    public static function totalBytesFor(string $tenantId, int $days = 30): array
    {
        $row = static::where('tenant_id', $tenantId)
            ->where('date', '>=', now()->subDays($days)->toDateString())
            ->selectRaw('SUM(bytes_in) as total_in, SUM(bytes_out) as total_out, SUM(request_count) as total_requests')
            ->first();

        return [
            'bytes_in'       => (int) ($row->total_in ?? 0),
            'bytes_out'      => (int) ($row->total_out ?? 0),
            'total_requests' => (int) ($row->total_requests ?? 0),
        ];
    }

    // Last 30 days of daily data for a chart
    public static function dailyFor(string $tenantId, int $days = 30): \Illuminate\Support\Collection
    {
        return static::where('tenant_id', $tenantId)
            ->where('date', '>=', now()->subDays($days)->toDateString())
            ->orderBy('date')
            ->get(['date', 'bytes_in', 'bytes_out', 'request_count']);
    }

    // Human-readable MB helper
    public static function toMb(int $bytes): float
    {
        return round($bytes / 1048576, 2);
    }
}