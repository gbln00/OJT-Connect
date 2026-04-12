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

    // Company relationship
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Reviewer (coordinator/admin who approved/rejected)
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Hour logs relationship (one-to-many)
    public function hourLogs()
    {
        return $this->hasMany(HourLog::class, 'application_id');
    }

    // Weekly reports relationship (one-to-many)
    public function weeklyReports()
    {
        return $this->hasMany(WeeklyReport::class, 'application_id');
    }

    // Evaluation relationship (one-to-one)
    public function evaluation()
    {
        return $this->hasOne(Evaluation::class, 'application_id');
    }

    // QR code relationship (one-to-one)
    public function qrClockIn()
    {
        return $this->hasOne(\App\Models\QrClockIn::class, 'application_id');
    }

    // Helpers
    public function isPending(): bool  { return $this->status === 'pending'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    // Accessors for total logged hours and remaining hours
    public function getTotalLoggedHoursAttribute(): float
    {
        return $this->hourLogs()->where('status', 'approved')->sum('total_hours');
    }

    // Remaining hours = required hours - total approved logged hours
    public function getRemainingHoursAttribute(): float
    {
        return max(0, $this->required_hours - $this->total_logged_hours);
    }

    // Accessor for human-readable status label
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'  => 'Pending Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default    => 'Unknown',
        };
    }

    // Scope: student's current active application
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'approved']);
    }
}