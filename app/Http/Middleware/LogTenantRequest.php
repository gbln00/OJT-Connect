<?php
namespace App\Http\Middleware;
 
use App\Models\TenantRequestLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
 
class LogTenantRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
 
        // Only log if we are inside a tenant context
        if (function_exists('tenant') && tenant()) {
            try {
                TenantRequestLog::create([
                    'tenant_id'     => tenant('id'),
                    'method'        => $request->method(),
                    'path'          => $request->path(),
                    'status_code'   => $response->getStatusCode(),
                    'response_size' => strlen($response->getContent()),
                    'ip'            => $request->ip(),
                    'logged_at'     => now(),
                ]);
            } catch (\Throwable $e) {
                // Never let logging break the tenant request
                \Log::error('TenantRequestLog failed: ' . $e->getMessage());
            }
        }
 
        return $response;
    }
}
