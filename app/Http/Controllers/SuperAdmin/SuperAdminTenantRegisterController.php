<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TenantRegistration;
use App\Services\RecaptchaService;
use App\Models\SuperAdminNotification;
use Illuminate\Http\Request;

class SuperAdminTenantRegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.tenant-register');
    }

    public function submit(Request $request, RecaptchaService $recaptcha)
    {
        $request->validate([
            'company_name'        => ['required', 'string', 'max:255'],
            'email'               => [
                'required',
                'email',
                'unique:tenants,email',
            ],
            'subdomain'           => [
                'required',
                'alpha_dash',
                'min:3',
                'max:30',
                'unique:tenants,id',
            ],
            'contact_person'      => ['required', 'string', 'max:255'],
            'phone'               => ['nullable', 'string', 'max:20'],
            'plan'                => ['required', 'in:basic,standard,premium'],
            'g-recaptcha-response'=> ['required'],
        ], [
            'g-recaptcha-response.required' => 'Please complete the CAPTCHA.',
        ]);

        // Verify reCAPTCHA
        if (!$recaptcha->verify($request->input('g-recaptcha-response'))) {
            return back()->withErrors([
                'g-recaptcha-response' => 'CAPTCHA verification failed. Please try again.',
            ])->withInput();
        }

        $payload = $request->only([
            'company_name', 'email', 'subdomain',
            'contact_person', 'phone', 'plan',
        ]);

        $existing = TenantRegistration::query()
            ->where('email', $request->email)
            ->orWhere('subdomain', $request->subdomain)
            ->first();

        if ($existing && $existing->status !== 'rejected') {
            return back()->withErrors([
                'email' => 'A registration using this email or subdomain is already under review or approved.',
            ])->withInput();
        }

        if ($existing && $existing->status === 'rejected') {
            $existing->update([
                ...$payload,
                'status' => 'pending',
                'rejection_reason' => null,
            ]);

            $registration = $existing->fresh();
        } else {
            $registration = TenantRegistration::create($payload);
        }

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