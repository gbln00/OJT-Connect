<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuperAdminNotification extends Model
{
    protected $connection = 'mysql';
    
    protected $fillable = [
        'type', 'title', 'message', 'icon', 'link', 'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ── Scopes ──
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ── Helper to create notifications cleanly ──
    public static function notify(
        string $type,
        string $title,
        string $message,
        string $icon = 'bell',
        ?string $link = null
    ): self {
        return self::create(compact('type', 'title', 'message', 'icon', 'link'));
    }
}