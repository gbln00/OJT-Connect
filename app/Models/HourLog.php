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

    // ── Helpers ───────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function getStatusClassAttribute(): string
    {
        return $this->status === 'approved' ? 'teal' : 'gold';
    }
}