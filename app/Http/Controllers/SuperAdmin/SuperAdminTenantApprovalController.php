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

        return view('super_admin.approvals.pending', compact('registrations'));
    }

    // Approve a registration → actually provision the tenant
    public function approve(TenantRegistration $registration)
    {
       
        $tenant = Tenant::create([
            'id'         => $registration->subdomain,   
            'name'       => $registration->company_name,
            'email'      => $registration->email,
            'plan'       => $registration->plan,
            'status'     => 'approved',
            'created_by' => null, 
        ]);

        
        $tenant->domains()->create([
            'domain' => $registration->subdomain . '.' . config('app.base_domain', 'yourapp.com'),
        ]);

        $registration->update(['status' => 'approved']);

        // ✅ Send approval email
        Mail::to($registration->email)->send(new TenantApproved($registration));

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

        // ✅ Send rejection email
        Mail::to($registration->email)->send(new TenantRejected($registration));

        return back()->with('info', "Registration rejected.");
    }
}