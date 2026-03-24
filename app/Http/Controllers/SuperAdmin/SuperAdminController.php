<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $totalTenants  = Tenant::count();
        $recentTenants = Tenant::latest()->take(5)->get();

        return view('super_admin.dashboard', compact('totalTenants', 'recentTenants'));
    }
}