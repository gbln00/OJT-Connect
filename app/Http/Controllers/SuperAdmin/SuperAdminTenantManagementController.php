<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class SuperAdminTenantManagementController extends Controller
{
    /**
     * List all tenants.
     */
    public function index()
    {
        $tenants = Tenant::with('domains')->latest()->paginate(15);

        return view('super_admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the create tenant form.
     */
    public function create()
    {
        return view('super_admin.tenants.create');
    }

    /**
     * Store a new tenant and its domain, then run migrations.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'id'             => ['required', 'string', 'max:255', 'unique:tenants,id', 'regex:/^[a-z0-9\-]+$/'],
            'domain'         => ['required', 'string', 'max:255', 'unique:domains,domain'],
            'admin_name'     => ['required', 'string', 'max:255'],
            'admin_email'    => ['required', 'email', 'max:255'],
            'admin_password' => ['required', 'string', 'min:8'],
        ], [
            'id.regex' => 'Tenant ID may only contain lowercase letters, numbers, and hyphens.',
        ]);

        $tenant = Tenant::create(['id' => $data['id']]);
        $tenant->domains()->create(['domain' => $data['domain']]);

        $tenant->run(function () use ($data) {
            \Artisan::call('tenants:migrate', [
                '--tenants' => [tenant('id')],
                '--force'   => true,
            ]);

            // Create the tenant's admin user after migrations run
            \App\Models\User::create([
                'name'     => $data['admin_name'],
                'email'    => $data['admin_email'],
                'password' => \Illuminate\Support\Facades\Hash::make($data['admin_password']),
                'role'     => 'admin',
            ]);
        });

        return redirect()
            ->route('super_admin.tenants.index')
            ->with('success', "Tenant \"{$tenant->id}\" created successfully.");
    }
    /**
     * Show a single tenant's details.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load('domains');

        return view('super_admin.tenants.show', compact('tenant'));
    }

    /**
     * Show the edit form for a tenant.
     */
    public function edit(Tenant $tenant)
    {
        $tenant->load('domains');

        return view('super_admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update a tenant's domain.
     * (Tenant ID is immutable — only the domain can be changed.)
     */
    public function update(Request $request, Tenant $tenant)
    {
        $tenant->load('domains');

        $currentDomain = $tenant->domains->first()?->domain;

        $data = $request->validate([
            'domain' => [
                'required',
                'string',
                'max:255',
                Rule::unique('domains', 'domain')->ignore($currentDomain, 'domain'),
            ],
        ]);

        // Update or create the primary domain
        if ($tenant->domains->first()) {
            $tenant->domains->first()->update(['domain' => $data['domain']]);
        } else {
            $tenant->domains()->create(['domain' => $data['domain']]);
        }

        return redirect()
            ->route('super_admin.tenants.index')
            ->with('success', "Tenant \"{$tenant->id}\" updated successfully.");
    }

    /**
     * Delete a tenant and all its data.
     */
    public function destroy(Tenant $tenant)
    {
        $tenantId = $tenant->id;

        // Stancl\Tenancy will cascade-delete the database and domains
        $tenant->delete();

        return redirect()
            ->route('super_admin.tenants.index')
            ->with('success', "Tenant \"{$tenantId}\" and all associated data have been deleted.");
    }
}