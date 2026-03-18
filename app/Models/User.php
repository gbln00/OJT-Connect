<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',  
        'is_active',
        'is_verified',
        'verified_at',
        'verified_by',
        'rejection_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verified_at'       => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'is_verified'       => 'boolean',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────

    public function studentProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OjtApplication::class, 'student_id');
    }

    public function verifier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ── Role helpers ──────────────────────────────────────────────

    public function isAdmin(): bool       { return $this->role === 'admin'; }
    public function isCoordinator(): bool { return $this->role === 'ojt_coordinator'; }
    public function isSupervisor(): bool  { return $this->role === 'company_supervisor'; }
    public function isStudent(): bool     { return $this->role === 'student_intern'; }

    // ── Accessors ─────────────────────────────────────────────────

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'              => 'Admin',
            'ojt_coordinator'    => 'Coordinator',
            'company_supervisor' => 'Supervisor',
            'student_intern'     => 'Student',
            default              => ucfirst($this->role),
        };
    }
}