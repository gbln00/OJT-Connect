<?php

namespace App\Http\Middleware;

use App\Models\TenantBandwidthLog;
use App\Models\TenantRequestLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogTenantRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (function_exists('tenant') && tenant()) {
            try {
                $tenantId    = tenant('id');
                $bytesIn     = strlen($request->getContent());
                $bytesOut    = strlen($response->getContent());
                $today       = now()->toDateString();

                // Existing per-request log (unchanged)
                TenantRequestLog::create([
                    'tenant_id'     => $tenantId,
                    'method'        => $request->method(),
                    'path'          => $request->path(),
                    'status_code'   => $response->getStatusCode(),
                    'response_size' => $bytesOut,
                    'ip'            => $request->ip(),
                    'logged_at'     => now(),
                ]);

                // Bandwidth aggregation — upsert today's row
                TenantBandwidthLog::upsert(
                    [[
                        'tenant_id'     => $tenantId,
                        'date'          => $today,
                        'bytes_in'      => $bytesIn,
                        'bytes_out'     => $bytesOut,
                        'request_count' => 1,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]],
                    uniqueBy: ['tenant_id', 'date'],
                    update: [
                        'bytes_in'      => \DB::raw("bytes_in + $bytesIn"),
                        'bytes_out'     => \DB::raw("bytes_out + $bytesOut"),
                        'request_count' => \DB::raw('request_count + 1'),
                        'updated_at'    => now(),
                    ]
                );

            } catch (\Throwable $e) {
                \Log::error('TenantRequestLog failed: ' . $e->getMessage());
            }
        }

        return $response;
    }
}