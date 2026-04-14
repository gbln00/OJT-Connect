<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantPlanHistory extends Model
{
    protected $connection = 'mysql';
    
    protected $fillable = [
        'tenant_id', 'plan_id', 'promotion_id',
        'price_paid', 'starts_at', 'ends_at', 'changed_by', 'notes',
    ];

    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime'];

    public function plan()      { return $this->belongsTo(Plan::class); }
    public function promotion() { return $this->belongsTo(PlanPromotion::class); }
    public function changedBy() { return $this->belongsTo(User::class, 'changed_by'); }
}
