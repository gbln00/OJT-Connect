<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'firstname',
        'lastname',
        'middlename',
        'course',
        'year_level',
        'section',
        'phone',
        'address',
        'required_hours',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ───────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        $middle = $this->middlename
            ? ' ' . strtoupper($this->middlename[0]) . '. '
            : ' ';

        return $this->firstname . $middle . $this->lastname;
    }

    public function getInitialsAttribute(): string
    {
        return strtoupper(substr($this->firstname, 0, 1) . substr($this->lastname, 0, 1));
    }

    // Available courses
    public static function courses(): array
    {
        return [
            'BS Information Technology',
            'BS Electronics',
            'BS Food Technology',
            'BS EMC',
            'BS Automotive Technology',
        ];
    }

    // Available year levels
    public static function yearLevels(): array
    {
        return [
            '1st Year',
            '2nd Year',
            '3rd Year',
            '4th Year',
        ];
    }
}