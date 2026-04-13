<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class TenantNotification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'message',
        'is_read',
        'target_role',
        'user_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where('target_role', $role);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Base query helper (role + user combined) ──────────────────

    public static function forAuthUser(): \Illuminate\Database\Eloquent\Builder
    {
        return static::forRole(auth()->user()->role)
                     ->forUser(auth()->id());
    }

    // ── Static helper ─────────────────────────────────────────────
    public static function notify(
        string  $title,
        string  $message,
        string  $type       = 'info',
        string  $targetRole = 'admin',
        ?int    $userId     = null
    ): self|Collection {

        // ── Targeted: one specific user ───────────────────────────
        if ($userId !== null) {
            return self::create([
                'title'       => $title,
                'message'     => $message,
                'type'        => $type,
                'target_role' => $targetRole,
                'user_id'     => $userId,
            ]);
        }

        // ── Broadcast: one row per user in the role ───────────────
        $users = User::where('role', $targetRole)->get();

        if ($users->isEmpty()) {
            return new Collection();
        }

        return $users->map(fn(User $user) => self::create([
            'title'       => $title,
            'message'     => $message,
            'type'        => $type,
            'target_role' => $targetRole,
            'user_id'     => $user->id,
        ]));
    }
}