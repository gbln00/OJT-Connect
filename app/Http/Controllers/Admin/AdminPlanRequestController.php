<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuperAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AdminPlanRequestController extends Controller
{
    /**
     * Handle a plan upgrade / downgrade request from the tenant admin.
     * Creates a super-admin notification and (optionally) sends an email.
     */
    public function store(Request $request)
    {
        $request->validate([
            'requested_plan' => ['required', 'string', 'max:50'],
            'request_type'   => ['required', 'in:upgrade,downgrade'],
            'message'        => ['required', 'string', 'max:2000'],
            'contact_email'  => ['required', 'email'],
        ]);

        $tenant      = tenancy()->tenant;
        $requestType = $request->request_type;
        $targetPlan  = $request->requested_plan;
        $user        = auth()->user();

        // ── Create super-admin notification ──────────────────────────────
        if (class_exists(SuperAdminNotification::class)) {
            SuperAdminNotification::notify(
                type:    'tenant',
                title:   ucfirst($requestType) . ' Request — ' . ($tenant?->id ?? 'unknown'),
                message: "Tenant \"" . ($tenant?->id ?? 'unknown') . "\" requested a plan {$requestType} to \"{$targetPlan}\". " .
                         "Contact: {$request->contact_email}. Message: " . \Str::limit($request->message, 120),
                icon:    $requestType === 'upgrade' ? 'arrow-up' : 'arrow-down',
                link:    route('super_admin.tenants.show', $tenant?->id ?? ''),
            );
        }

        // ── Send email to super-admin support ────────────────────────────
        try {
            Mail::raw(
                "Plan " . ucfirst($requestType) . " Request\n\n" .
                "Institution: " . ($tenant?->id ?? 'unknown') . "\n" .
                "Current Plan: " . ($tenant?->plan ?? 'N/A') . "\n" .
                "Requested Plan: {$targetPlan}\n" .
                "Requested by: {$user->name} <{$request->contact_email}>\n\n" .
                "Message:\n{$request->message}",
                fn($mail) => $mail
                    ->to(config('mail.from.address', 'support@ojtconnect.com'))
                    ->subject("Plan " . ucfirst($requestType) . " Request — " . ($tenant?->id ?? 'unknown'))
                    ->replyTo($request->contact_email, $user->name)
            );
        } catch (\Throwable $e) {
            \Log::error('Plan request email failed: ' . $e->getMessage());
        }

        return back()->with('success',
            'Your ' . $requestType . ' request has been submitted. ' .
            'Our team will contact you at ' . $request->contact_email . ' shortly.'
        );
    }
}