<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPromotion extends Model
{
    protected $connection = 'mysql';
    
    protected $fillable = [
        'plan_id', 'code', 'label', 'discount_type',
        'discount_value', 'starts_at', 'ends_at',
        'max_uses', 'used_count', 'is_active',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function plan() { return $this->belongsTo(Plan::class); }

    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->ends_at && now()->gt($this->ends_at)) return false;
        return true;
    }

    public function getDiscountLabelAttribute(): string
    {
        return $this->discount_type === 'percent'
            ? "{$this->discount_value}% off"
            : '₱' . number_format($this->discount_value) . ' off';
    }
}