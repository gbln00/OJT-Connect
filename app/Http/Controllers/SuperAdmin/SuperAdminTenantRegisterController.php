<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TenantRegistration;
use App\Models\SuperAdminNotification;  // ← add this
use Illuminate\Http\Request;

class SuperAdminTenantRegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.tenant-register');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'company_name'   => ['required', 'string', 'max:255'],
            'email'          => [
                'required', 
                'email', 
                'unique:tenant_registrations,email',
                'unique:tenants,email',                 // 👈 check active tenants
            ],
            'subdomain'      => [
                'required', 
                'alpha_dash', 
                'min:3', 
                'max:30', 
                'unique:tenant_registrations,subdomain',
                'unique:tenants,id',                    // 👈 check active tenants
            ],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'plan'           => ['required', 'in:basic,standard,premium'],
        ]);

        $registration = TenantRegistration::create($request->only([
            'company_name', 'email', 'subdomain',
            'contact_person', 'phone', 'plan',
        ]));

        SuperAdminNotification::notify(
            type:    'registration',
            title:   'New Registration Submitted',
            message: "\"{$registration->company_name}\" submitted a new tenant registration.",
            icon:    'bell',
            link:    route('super_admin.approvals.pending'),
        );

        return redirect()->back()->with('success',
            'Registration submitted! We will review and notify you via email within 24 hours.'
        );
    }
}