<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantUpdate extends Model
{
    protected $connection = 'mysql'; // Lives in central DB, not tenant DB

    protected $fillable = [
        'tenant_id', 'version_id', 'status',
        'installed_at', 'installed_by', 'error_log', 'migration_batch',
    ];

    protected $casts = [
        'installed_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function version()
    {
        return $this->belongsTo(SystemVersion::class, 'version_id');
    }

    // ── Status helpers ─────────────────────────────────────────────

    public function isPending(): bool     { return $this->status === 'pending'; }
    public function isInProgress(): bool  { return $this->status === 'in_progress'; }
    public function isCompleted(): bool   { return $this->status === 'completed'; }
    public function isFailed(): bool      { return $this->status === 'failed'; }

    public function statusColor(): string
    {
        return match($this->status) {
            'pending'     => 'steel',
            'in_progress' => 'gold',
            'completed'   => 'teal',
            'failed'      => 'coral',
            default       => 'steel',
        };
    }

    public function statusIcon(): string
    {
        return match($this->status) {
            'pending'     => '⏳',
            'in_progress' => '🔄',
            'completed'   => '✅',
            'failed'      => '❌',
            default       => '—',
        };
    }
}