<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TenantRegistration;
use Stancl\Tenancy\Database\Models\Domain;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SuperAdminTenantApprovalController extends Controller
{
    // List all pending registrations
    public function pending()
    {
        $registrations = TenantRegistration::where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('superadmin.tenants.pending', compact('registrations'));
    }

    // Approve a registration → actually provision the tenant
    public function approve(TenantRegistration $registration)
    {
        // 1. Create the tenant record
        $tenant = Tenant::create([
            'id'         => $registration->subdomain,   // used as tenant identifier
            'name'       => $registration->company_name,
            'email'      => $registration->email,
            'plan'       => $registration->plan,
            'status'     => 'approved',
            'created_by' => null, // null = came from self-registration
        ]);

        // 2. Attach the domain
        $tenant->domains()->create([
            'domain' => $registration->subdomain . '.' . config('app.base_domain', 'yourapp.com'),
        ]);

        // 3. Run tenant migrations
        // tenancy()->initialize($tenant);
        // Artisan::call('tenants:migrate', ['--tenants' => [$tenant->id]]);
        // tenancy()->end();

        // 4. Mark registration as approved
        $registration->update(['status' => 'approved']);

        // TODO: Send approval email to $registration->email

        return back()->with('success', "Tenant '{$registration->company_name}' provisioned successfully.");
    }

    // Reject a registration
    public function reject(Request $request, TenantRegistration $registration)
    {
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $registration->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // TODO: Send rejection email

        return back()->with('info', "Registration rejected.");
    }
}