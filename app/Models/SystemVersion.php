<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TenantUpdate;

class SystemVersion extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'version', 'label', 'type', 'changelog',
        'is_published', 'is_current', 'is_critical',
        'published_at', 'created_by',
        // New fields:
        'github_release_id', 'github_tag', 'github_html_url',
        'migration_folder', 'requires_version',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_current'   => 'boolean',
        'is_critical'  => 'boolean',
        'published_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function readReceipts()
    {
        return $this->hasMany(VersionReadReceipt::class, 'version_id');
    }

    // NEW: link to per-tenant install records
    public function tenantUpdates()
    {
        return $this->hasMany(TenantUpdate::class, 'version_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderByDesc('published_at');
    }

    // NEW: only critical uninstalled updates
    public function scopeCriticalPending($query, string $tenantId)
    {
        return $query->where('is_critical', true)
                     ->where('is_published', true)
                     ->whereHas('tenantUpdates', function ($q) use ($tenantId) {
                         $q->where('tenant_id', $tenantId)
                           ->where('status', 'pending');
                     });
    }

    // ── Helpers ────────────────────────────────────────────────────

    public function typeColor(): string
    {
        return match($this->type) {
            'major'  => 'coral',
            'minor'  => 'blue',
            'patch'  => 'teal',
            'hotfix' => 'gold',
            default  => 'steel',
        };
    }

    public function typeIcon(): string
    {
        return match($this->type) {
            'major'  => '🚀',
            'minor'  => '✨',
            'patch'  => '🔧',
            'hotfix' => '🩹',
            default  => '📦',
        };
    }

    public function isReadByTenant(string $tenantId, string $email): bool
    {
        return VersionReadReceipt::where([
            'version_id' => $this->id,
            'tenant_id'  => $tenantId,
            'read_by'    => $email,
        ])->exists();
    }

    public function readTenantCount(): int
    {
        return $this->readReceipts()->distinct('tenant_id')->count('tenant_id');
    }

    // NEW: how many tenants have completed this install
    public function installedTenantCount(): int
    {
        return $this->tenantUpdates()->where('status', 'completed')->count();
    }

    // NEW: get the TenantUpdate record for a specific tenant
    public function tenantUpdateFor(string $tenantId): ?TenantUpdate
    {
        return $this->tenantUpdates()->where('tenant_id', $tenantId)->first();
    }

    public function markAsCurrent(): void
    {
        static::where('is_current', true)->update(['is_current' => false]);
        $this->update(['is_current' => true]);
    }

    public static function current(): ?self
    {
        return static::where('is_current', true)->first()
            ?? static::published()->first();
    }

    // NEW: find the version that must be installed before this one
    public function requiredVersion(): ?self
    {
        if (! $this->requires_version) return null;
        return static::where('version', $this->requires_version)->first();
    }
}