<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    protected $fillable = [
        'student_id',
        'application_id',
        'supervisor_id',
        'attendance_rating',
        'performance_rating',
        'overall_grade',
        'recommendation',
        'remarks',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at'       => 'datetime',
        'overall_grade'      => 'decimal:2',
        'attendance_rating'  => 'integer',
        'performance_rating' => 'integer',
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

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function isPassed(): bool
    {
        return $this->recommendation === 'pass';
    }

    public function getRecommendationClassAttribute(): string
    {
        return $this->recommendation === 'pass' ? 'teal' : 'coral';
    }

    public function getRecommendationLabelAttribute(): string
    {
        return ucfirst($this->recommendation);
    }

    public function getGradeColorAttribute(): string
    {
        if ($this->overall_grade >= 90) return 'var(--teal)';
        if ($this->overall_grade >= 75) return 'var(--blue)';
        if ($this->overall_grade >= 60) return 'var(--gold)';
        return 'var(--coral)';
    }

    public function getRatingLabelAttribute(): string
    {
        $avg = ($this->attendance_rating + $this->performance_rating) / 2;
        return match(true) {
            $avg >= 5 => 'Excellent',
            $avg >= 4 => 'Very Good',
            $avg >= 3 => 'Good',
            $avg >= 2 => 'Fair',
            default   => 'Poor',
        };
    }
}