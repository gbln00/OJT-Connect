<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Models\StudentProfile;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
    // Role helpers
    public function isAdmin(): bool { 
        return $this->role === 'admin'; 
    }

    public function isCoordinator(): bool { 
        return $this->role === 'ojt_coordinator'; 
    }

    public function isSupervisor(): bool { 
        return $this->role === 'company_supervisor'; 
    }

    public function isStudent(): bool { 
        return $this->role === 'student_intern'; 
    }
    
    // Relationships
    public function applications()
    {
        return $this->hasMany(OjtApplication::class, 'student_id');
    }

    public function activeApplication()
    {
        return $this->hasOne(OjtApplication::class, 'student_id')
                    ->whereIn('status', ['pending', 'approved'])
                    ->latest();           
    }

    
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

}
