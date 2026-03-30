<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TenantRegistration;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\SuperAdminNotification;

class SuperAdminTenantManagementController extends Controller
{
    /**
     * List all tenants.
     */
    public function index()
    {
        $tenants       = Tenant::with('domains')->latest()->paginate(15);
        $totalCount    = Tenant::count();
        $activeCount   = Tenant::where('status', 'active')->orWhereNull('status')->count();
        $inactiveCount = Tenant::where('status', 'inactive')->count();
        $domainCount   = \Stancl\Tenancy\Database\Models\Domain::count();

        return view('super_admin.tenants.index', compact(
            'tenants', 'totalCount', 'activeCount', 'inactiveCount', 'domainCount'
        ));
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

            // Seed the default admin
            (new \Database\Seeders\TenantAdminSeeder)->run(
                name:     $data['admin_name'],
                email:    $data['admin_email'],
                password: $data['admin_password'],
            );

            // 🔔 Notification
            SuperAdminNotification::notify(
                type:    'tenant',
                title:   'New Tenant Created',
                message: "Tenant \"{$data['id']}\" was manually created by super admin.",
                icon:    'plus',
                link:    route('super_admin.tenants.show', $data['id']),
            );
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
     * Update a tenant's domain, status, and plan.
     *
     * Domain is OPTIONAL — the quick-toggle on the index page does not
     * send a domain field, so we must not require it here.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $tenant->load('domains');

        $currentDomain = $tenant->domains->first()?->domain;

        $data = $request->validate([
            // nullable so the inline status toggle (which omits domain) passes validation
            'domain' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('domains', 'domain')->ignore($currentDomain, 'domain'),
            ],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'plan'   => ['nullable', 'string', Rule::in(['basic', 'standard', 'premium'])],
        ]);

        // ── Capture old status BEFORE updating so the comparison is accurate ──
        $oldStatus = $tenant->status ?? 'active';

        // Only update domain if one was actually submitted
        if (!empty($data['domain'])) {
            if ($tenant->domains->first()) {
                $tenant->domains->first()->update(['domain' => $data['domain']]);
            } else {
                $tenant->domains()->create(['domain' => $data['domain']]);
            }
        }

        // Update tenant meta columns
        $tenant->update([
            'status' => $data['status'],
            'plan'   => $data['plan'] ?? null,
        ]);

        // 🔔 Notification — only fires when status actually changed
        $newStatus = $data['status'];

        if ($oldStatus !== $newStatus) {
            SuperAdminNotification::notify(
                type:    'status',
                title:   'Tenant ' . ($newStatus === 'active' ? 'Activated' : 'Deactivated'),
                message: "Tenant \"{$tenant->id}\" was " . ($newStatus === 'active' ? 'activated' : 'deactivated') . ".",
                icon:    'toggle',
                link:    route('super_admin.tenants.show', $tenant),
            );
        }

        // Redirect back to wherever the request came from (index toggle or edit form)
        $redirectTo = $request->input('redirect_to', 'index');

        if ($redirectTo === 'edit') {
            return redirect()
                ->route('super_admin.tenants.edit', $tenant)
                ->with('success', "Tenant \"{$tenant->id}\" updated successfully.");
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

        // Delete the matching registration record too
        TenantRegistration::where('subdomain', $tenant->id)
            ->orWhere('email', $tenant->email)
            ->delete();

        // Stancl\Tenancy will cascade-delete the database and domains
        $tenant->delete();

        return redirect()
            ->route('super_admin.tenants.index')
            ->with('success', "Tenant \"{$tenantId}\" and all associated data have been deleted.");
    }
}