<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeeklyReport extends Model
{
    protected $fillable = [
        'student_id',
        'application_id',
        'week_number',
        'week_start',
        'week_end',
        'description',
        'file_path',
        'status',
        'feedback',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'week_start'  => 'date',
        'week_end'    => 'date',
        'reviewed_at' => 'datetime',
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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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

    public function isReturned(): bool
    {
        return $this->status === 'returned';
    }

    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'approved' => 'teal',
            'returned' => 'coral',
            default    => 'gold',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getDateRangeAttribute(): string
    {
        return $this->week_start->format('M d') . ' – ' . $this->week_end->format('M d, Y');
    }
}