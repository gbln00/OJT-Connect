<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SystemVersion extends Model
{
    protected $connection = 'mysql';

    protected $fillable = [
        'version', 'label', 'type', 'changelog',
        'is_published', 'published_at', 'created_by',
    ];

    protected $casts = [
        'is_published'  => 'boolean',
        'published_at'  => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────
    public function readReceipts()
    {
        return $this->hasMany(VersionReadReceipt::class, 'version_id');
    }

    // ── Scopes ─────────────────────────────────────────────────
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderByDesc('published_at');
    }

    // ── Helpers ─────────────────────────────────────────────────
    public function typeColor(): string
    {
        return match($this->type) {
            'major'   => 'coral',
            'minor'   => 'blue',
            'patch'   => 'teal',
            'hotfix'  => 'gold',
            default   => 'steel',
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

    //
    public static function current(): ?self
    {
        return static::published()->first(); // already ordered by published_at desc
    }
}

