<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OjtApplication extends Model
{
    use HasFactory;

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

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function hourLogs()
    {
        return $this->hasMany(HourLog::class, 'application_id');
    }

    public function weeklyReports()
    {
        return $this->hasMany(WeeklyReport::class, 'application_id');
    }

    public function evaluation()
    {
        return $this->hasOne(Evaluation::class, 'application_id');
    }

    public function qrClockIn()
    {
        return $this->hasOne(\App\Models\QrClockIn::class, 'application_id');
    }

    // Status helpers
    public function isPending(): bool      { return $this->status === 'pending'; }
    public function isApproved(): bool     { return $this->status === 'approved'; }
    public function isRejected(): bool     { return $this->status === 'rejected'; }
    public function isUnderReview(): bool  { return $this->status === 'under_review'; }

    // Computed hour attributes
    public function getTotalLoggedHoursAttribute(): float
    {
        return $this->hourLogs()->where('status', 'approved')->sum('total_hours');
    }

    public function getRemainingHoursAttribute(): float
    {
        return max(0, $this->required_hours - $this->total_logged_hours);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'      => 'Pending Review',
            'under_review' => 'Under Review',
            'approved'     => 'Approved',
            'rejected'     => 'Rejected',
            default        => 'Unknown',
        };
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'under_review', 'approved']);
    }
}