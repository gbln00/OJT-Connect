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
        'company_id',
        'google_id',  
        'avatar',   
        
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

    /**
 * Get a human-readable role label.
 */
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

    // A user with role 'student_intern' can have many OJT applications
    public function applications()
    {
        return $this->hasMany(OjtApplication::class, 'student_id');
    }

    // Get the active application (pending or approved) for the student
    public function activeApplication()
    {
        return $this->hasOne(OjtApplication::class, 'student_id')
                    ->whereIn('status', ['pending', 'approved'])
                    ->latest();           
    }

    // A user with role 'student_intern' has one student profile
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    // A user with role 'company_supervisor' belongs to a company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    

}
