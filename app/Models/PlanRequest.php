<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PlanRequest extends Model
{
    protected $connection = 'mysql'; 

    protected $fillable = [
        'tenant_id',
        'current_plan',
        'requested_plan',
        'request_type',
        'contact_email',
        'message',
        'status',
        'admin_notes',
        'actioned_at',
    ];

    protected $casts = [
        'actioned_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}