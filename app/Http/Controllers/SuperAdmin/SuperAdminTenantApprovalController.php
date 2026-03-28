<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TenantRegistration;
use App\Models\Tenant;
use App\Models\SuperAdminNotification;
use App\Mail\TenantApproved;
use App\Mail\TenantRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SuperAdminTenantApprovalController extends Controller
{
    public function pending()
    {
        $registrations = TenantRegistration::where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('super_admin.approvals.pending', compact('registrations'));
    }

    public function approve(TenantRegistration $registration)
    {
        $tenant = Tenant::create([
            'id'         => $registration->subdomain,
            'name'       => $registration->company_name,
            'email'      => $registration->email,
            'plan'       => $registration->plan,
            'status'     => 'active',
            'created_by' => null,
        ]);

        $tenant->domains()->create([
            'domain' => $registration->subdomain . '.' . config('app.base_domain', 'ojtconnect.com'),
        ]);

        $registration->update(['status' => 'approved']);

        Mail::to($registration->email)->send(new TenantApproved($registration));

        // 🔔 Notification
        SuperAdminNotification::notify(
            type:    'approval',
            title:   'Tenant Approved',
            message: "\"{$registration->company_name}\" has been approved and provisioned.",
            icon:    'check',
            link:    route('super_admin.tenants.show', $registration->subdomain),
        );

        return back()->with('success', "Tenant '{$registration->company_name}' provisioned successfully.");
    }

    public function reject(Request $request, TenantRegistration $registration)
    {
        $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $registration->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        Mail::to($registration->email)->send(new TenantRejected($registration));

        // 🔔 Notification
        SuperAdminNotification::notify(
            type:    'approval',
            title:   'Registration Rejected',
            message: "\"{$registration->company_name}\" registration was rejected.",
            icon:    'x',
            link:    route('super_admin.approvals.pending'),
        );

        return back()->with('info', "Registration rejected.");
    }
}