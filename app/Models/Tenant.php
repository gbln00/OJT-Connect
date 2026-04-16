<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Carbon\Carbon;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    public static function getCustomColumns(): array
    {
        return [
            'id', 'name', 'email', 'plan', 'status', 'created_by',
            'plan_expires_at', 'plan_grace', 'grace_started_at',
        ];
    }

    protected $fillable = [
        'id', 'name', 'email', 'plan', 'status', 'created_by',
        'plan_expires_at', 'plan_grace', 'grace_started_at',
    ];

    protected $casts = [
        'plan_expires_at'  => 'datetime',
        'plan_grace'       => 'boolean',
        'grace_started_at' => 'datetime',
    ];

    /** True if subscription has expired (past plan_expires_at) */
    public function subscriptionExpired(): bool
    {
        if (! $this->plan_expires_at) return false;
        return now()->gt($this->plan_expires_at);
    }

    /** True if still within the 7-day grace window */
    public function inGracePeriod(): bool
    {
        if (! $this->plan_expires_at) return false;
        $graceEnd = $this->plan_expires_at->copy()->addDays(7);
        return now()->gt($this->plan_expires_at) && now()->lte($graceEnd);
    }

    /** Days remaining before expiry (negative = overdue) */
    public function daysUntilExpiry(): ?int
    {
        if (! $this->plan_expires_at) return null;
        return (int) now()->startOfDay()->diffInDays($this->plan_expires_at, false);
    }

    /** Set plan + expiry at once (called on plan approval) */
    public function assignPlan(string $planName, ?Carbon $expiresAt = null): void
    {
        $plan = \App\Models\Plan::where('name', $planName)->first();
        $days = ($plan?->billing_cycle === 'monthly') ? 30 : 365;

        $this->update([
            'plan'           => $planName,
            'plan_expires_at' => $expiresAt ?? now()->addDays($days),
            'plan_grace'     => false,
            'grace_started_at'=> null,
        ]);
    }
    public function checkSubscriptionExpired(): bool
    {
        if (! $this->plan_expires_at) return false;
        return now()->gt($this->plan_expires_at);
    }

    public function checkInGracePeriod(): bool
    {
        if (! $this->plan_expires_at) return false;

        $graceEnd = $this->plan_expires_at->copy()->addDays(7);

        return now()->gt($this->plan_expires_at) && now()->lte($graceEnd);
    }

    public function checkDaysUntilExpiry(): ?int
    {
        if (! $this->plan_expires_at) return null;
        return (int) now()->startOfDay()->diffInDays($this->plan_expires_at, false);
    }
  
}