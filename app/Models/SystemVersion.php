<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemVersion extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'version', 'label', 'type', 'changelog',
        'is_published', 'is_current', 'published_at', 'created_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_current'   => 'boolean',
        'published_at' => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────
    public function readReceipts()
    {
        return $this->hasMany(VersionReadReceipt::class, 'version_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderByDesc('published_at');
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

    /** How many distinct tenants have read this version */
    public function readTenantCount(): int
    {
        return $this->readReceipts()->distinct('tenant_id')->count('tenant_id');
    }

    /** Mark this version as the live/current release (atomically) */
    public function markAsCurrent(): void
    {
        static::where('is_current', true)->update(['is_current' => false]);
        $this->update(['is_current' => true]);
    }

    /** Latest published version = "current" */
    public static function current(): ?self
    {
        // Prefer explicit is_current flag; fall back to latest published
        return static::where('is_current', true)->first()
            ?? static::published()->first();
    }
}