<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class QrClockIn extends Model
{
    protected $fillable = [
        'company_id',
        'supervisor_id',
        'token',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relationships ─────────────────────────────────────────────

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // ── Helpers ───────────────────────────────────────────────────

    public static function generateToken(): string
    {
        return hash('sha256', Str::random(40) . now()->timestamp);
    }

    public function isUsable(): bool
    {
        return $this->is_active;
    }

    /**
     * AM before 12:00, PM from 12:00 onwards.
     */
    public static function currentSession(): string
    {
        return now()->hour < 12 ? 'morning' : 'afternoon';
    }

    public function scanUrl(): string
    {
        return route('qr.scan', ['token' => $this->token]);
    }
}