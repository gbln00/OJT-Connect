<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TenantRegistration;
use Illuminate\Http\Request;

class SuperAdminTenantRegisterController extends Controller
{
    // Show the public signup form
    public function showForm()
    {
        return view('auth.tenant-register');
    }

    // Handle the form submission
    public function submit(Request $request)
    {
        $request->validate([
            'company_name'   => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'unique:tenant_registrations,email'],
            'subdomain'      => ['required', 'alpha_dash', 'min:3', 'max:30', 'unique:tenant_registrations,subdomain'],
            'contact_person' => ['required', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'plan'           => ['required', 'in:basic,standard,premium'],
        ]);

        TenantRegistration::create($request->only([
            'company_name', 'email', 'subdomain',
            'contact_person', 'phone', 'plan',
        ]));

        // TODO: Notify SuperAdmin via mail/notification here

        return redirect()->back()->with('success',
            'Registration submitted! We will review and notify you via email within 24 hours.'
        );
    }
}