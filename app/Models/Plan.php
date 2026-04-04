<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $connection = 'mysql'; 

    protected $fillable = [
        'name', 'label', 'base_price', 'billing_cycle',
        'student_cap', 'features', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'features'   => 'array',
        'is_active'  => 'boolean',
        'student_cap'=> 'integer',
    ];

    public function promotions()
    {
        return $this->hasMany(PlanPromotion::class);
    }

    public function activePromotions()
    {
        return $this->promotions()
            ->where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()));
    }

    public function finalPrice(?PlanPromotion $promo = null): int
    {
        if (!$promo) return $this->base_price;

        return $promo->discount_type === 'percent'
            ? (int) ($this->base_price * (1 - $promo->discount_value / 100))
            : max(0, $this->base_price - $promo->discount_value);
    }

    public function hasFeature(string $key): bool
    {
        return (bool) ($this->features[$key] ?? false);
    }
}