<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicketReply extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'sender_type',
        'sender_name',
        'message',
        'attachment_path',
        'attachment_name',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function isFromSupport(): bool
    {
        return $this->sender_type === 'support';
    }

    public function isFromUser(): bool
    {
        return $this->sender_type === 'user';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->sender_type === 'support') {
            return 'Support Team';
        }
        return $this->sender_name ?? $this->user?->name ?? 'User';
    }
}