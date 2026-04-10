<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantNotification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'is_read',
        'target_role',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where('target_role', $role);
    }

    // ── Static helper ─────────────────────────────────────────────

    public static function notify(
        string  $title,
        string  $message,
        string  $type       = 'info',
        string  $targetRole = 'admin'   // always required now — no shared notifications
    ): self {
        return self::create([
            'title'       => $title,
            'message'     => $message,
            'type'        => $type,
            'target_role' => $targetRole,
        ]);
    }
}