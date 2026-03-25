<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuperAdminTenantManualController extends Controller
{
    public function create()
    {
        return view('superadmin.tenants.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email'],
            'subdomain' => ['required', 'alpha_dash', 'min:3', 'max:30', 'unique:tenants,id'],
            'plan'      => ['required', 'in:basic,pro'],
        ]);

        // Immediately provision — no approval needed
        $tenant = Tenant::create([
            'id'         => $request->subdomain,
            'name'       => $request->name,
            'email'      => $request->email,
            'plan'       => $request->plan,
            'status'     => 'approved',
            'created_by' => Auth::id(), // SuperAdmin who created it
        ]);

        $tenant->domains()->create([
            'domain' => $request->subdomain . '.' . config('app.base_domain', 'yourapp.com'),
        ]);

        // Run migrations for this tenant
        // tenancy()->initialize($tenant);
        // Artisan::call('tenants:migrate', ['--tenants' => [$tenant->id]]);
        // tenancy()->end();

        return redirect()->route('superadmin.tenants.index')
            ->with('success', "Tenant '{$request->name}' created and ready.");
    }
}