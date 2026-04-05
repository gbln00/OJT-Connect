<?php
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
 
class TenantRequestLog extends Model
{   
    protected $connection = 'mysql';
    
    public $timestamps = false;
 
    protected $fillable = [
        'tenant_id', 'method', 'path',
        'status_code', 'response_size', 'ip', 'logged_at',
    ];
 
    protected $casts = [
        'logged_at' => 'datetime',
    ];
 
    // Get the most recent log entry for a given tenant
    public static function lastActivityFor(string $tenantId): ?self
    {
        return static::where('tenant_id', $tenantId)
            ->latest('logged_at')
            ->first();
    }
 
    // Count requests for a tenant in the last N days
    public static function recentCountFor(string $tenantId, int $days = 7): int
    {
        return static::where('tenant_id', $tenantId)
            ->where('logged_at', '>=', now()->subDays($days))
            ->count();
    }
}
