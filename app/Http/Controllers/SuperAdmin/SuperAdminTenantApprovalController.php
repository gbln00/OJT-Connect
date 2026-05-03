<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TenantRegistration;
use App\Models\Tenant;
use App\Models\SuperAdminNotification;
use App\Mail\TenantApproved;
use App\Mail\TenantRejected;
use App\Jobs\ApproveTenantJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

class SuperAdminTenantApprovalController extends Controller
{
    public function pending()
    {
        // Show pending tenant registrations
        $registrations = TenantRegistration::where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('super_admin.approvals.pending', compact('registrations'));
    }

    public function approve(Request $request, TenantRegistration $registration)
    {
        if ($registration->status !== 'pending') {
            return back()->with('error', 'This registration has already been processed.');
        }

        if (DB::table('tenants')->where('id', $registration->subdomain)->exists()) {
            return back()->with('error', 'A tenant with this subdomain already exists.');
        }

        $tenant = Tenant::create([
            'id'         => $registration->subdomain,
            'name'       => $registration->company_name,
            'email'      => $registration->email,
            'plan'       => $registration->plan,
            'status'     => 'active',
            'created_by' => null,
        ]);

        $this->ensureTenantDomains($tenant, $registration->subdomain, $request);

        $registration->update(['status' => 'approved']);

        // Dispatch to queue — returns immediately, worker handles the rest
        ApproveTenantJob::dispatch($registration, $tenant);

        \App\Models\SuperAdminNotification::notify(
            type:    'approval',
            title:   'Tenant Approved',
            message: "\"{$registration->company_name}\" has been approved and is being provisioned.",
            icon:    'check',
            link:    route('super_admin.tenants.show', $registration->subdomain),
        );

        return back()->with('success', "Tenant '{$registration->company_name}' approved! Provisioning in background.");
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

        SuperAdminNotification::notify(
            type:    'approval',
            title:   'Registration Rejected',
            message: "\"{$registration->company_name}\" registration was rejected.",
            icon:    'x',
            link:    route('super_admin.approvals.pending'),
        );

        return back()->with('info', "Registration rejected.");
    }

    private function ensureTenantDomains(Tenant $tenant, string $subdomain, ?Request $request = null): void
    {
        $baseDomains = [];

        $baseDomains[] = 'ojt-connect.xyz';
        $baseDomains[] = 'ojt-connect.onrender.com';
        $baseDomains[] = 'localhost';
        $baseDomains[] = '127.0.0.1';

        if ($request) {
            $requestHost = preg_replace('/:\d+$/', '', $request->getHost()) ?: '';
            if ($requestHost !== '' && !in_array($requestHost, $baseDomains)) {
                $baseDomains[] = $requestHost;
            }
        }

        $domains = collect($baseDomains)
            ->filter()
            ->map(fn (string $domain) => strtolower(trim($domain)))
            ->map(function (string $domain) {
                $domain = preg_replace('#^https?://#', '', $domain);
                return rtrim($domain, '/');
            })
            ->unique()
            ->map(fn (string $baseDomain) => "{$subdomain}.{$baseDomain}");

        foreach ($domains as $domain) {
            Domain::firstOrCreate([
                'domain' => $domain,
            ], [
                'tenant_id' => $tenant->getTenantKey(),
            ]);
        }
    }
}