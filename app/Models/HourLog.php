<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HourLog extends Model
{
    protected $fillable = [
        'student_id',
        'application_id',
        'date',
        'session',
        'time_in',
        'time_out',
        'total_hours',
        'description',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'date'        => 'date',
        'total_hours' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(OjtApplication::class, 'application_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeMorning($query)
    {
        return $query->where('session', 'morning');
    }

    public function scopeAfternoon($query)
    {
        return $query->where('session', 'afternoon');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function getSessionLabelAttribute(): string
    {
        return $this->session === 'morning' ? 'Morning' : 'Afternoon';
    }

    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'approved' => 'teal',
            'rejected' => 'coral',
            default    => 'gold',
        };
    }
}