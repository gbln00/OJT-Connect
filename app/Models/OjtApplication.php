<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\HourLog;
use App\Models\WeeklyReport;
use App\Models\Evaluation;

class OjtApplication extends Model
{
    protected $table = 'applications'; 

    protected $fillable = [
        'student_id',
        'company_id',
        'reviewed_by',
        'program',
        'school_year',
        'semester',
        'required_hours',
        'document_path',
        'status',
        'remarks',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    // ── Relationships ────────────────────────────────────────────

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ── Helpers ──────────────────────────────────────────────────

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

    public function getStatusLabelAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'approved' => 'teal',
            'rejected' => 'coral',
            default    => 'gold',
        };
    }
    // ── Hour Logs Relationship ─────────────────────────────────────────────
    public function hourLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HourLog::class, 'application_id');
    }

    // ── Weekly Reports Relationship ────────────────────────────────────────────
    public function weeklyReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WeeklyReport::class, 'application_id');
    }

    // ── Evaluations Relationship ────────────────────────────────────────────
        public function evaluations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Evaluation::class, 'application_id');
    }
}