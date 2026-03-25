<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantRegistration extends Model
{
    protected $fillable = [
        'company_name', 'email', 'subdomain',
        'contact_person', 'phone', 'plan', 'status', 'rejection_reason',
    ];
}