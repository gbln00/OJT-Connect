<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'type',
        'priority',
        'status',
        'module',
        'internal_note',
        'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->orderBy('created_at');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_on_user']);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Status helpers ────────────────────────────────────────────

    public function isOpen(): bool       { return $this->status === 'open'; }
    public function isInProgress(): bool { return $this->status === 'in_progress'; }
    public function isWaiting(): bool    { return $this->status === 'waiting_on_user'; }
    public function isResolved(): bool   { return $this->status === 'resolved'; }
    public function isClosed(): bool     { return $this->status === 'closed'; }
    public function isActive(): bool     { return in_array($this->status, ['open', 'in_progress', 'waiting_on_user']); }

    // ── Label helpers ─────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'            => 'Open',
            'in_progress'     => 'In Progress',
            'waiting_on_user' => 'Waiting on You',
            'resolved'        => 'Resolved',
            'closed'          => 'Closed',
            default           => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open'            => 'blue',
            'in_progress'     => 'gold',
            'waiting_on_user' => 'coral',
            'resolved'        => 'teal',
            'closed'          => 'steel',
            default           => 'steel',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'bug'             => 'Bug Report',
            'feature_request' => 'Feature Request',
            'general_inquiry' => 'General Inquiry',
            'billing'         => 'Billing',
            'account'         => 'Account',
            'other'           => 'Other',
            default           => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return ucfirst($this->priority);
    }

    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'coral',
            'high'   => 'gold',
            'normal' => 'blue',
            'low'    => 'steel',
            default  => 'steel',
        };
    }

    /**
     * Ticket reference number shown to users: e.g. TKT-00042
     */
    public function getRefAttribute(): string
    {
        return 'TKT-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }
}