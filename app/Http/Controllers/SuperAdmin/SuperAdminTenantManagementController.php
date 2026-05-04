<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\TenantRegistration;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\PlanPromotion;
use App\Models\TenantPlanHistory;
use App\Models\SuperAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuperAdminTenantManagementController extends Controller
{
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

    public function create()
    {
        $plans      = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $baseDomain = 'ojt-connect.xyz';
        return view('super_admin.tenants.create', compact('plans', 'baseDomain'));
    }

    public function store(Request $request)
    {
        $baseDomain = 'ojt-connect.xyz';

        $data = $request->validate([
            'id'               => ['required', 'string', 'max:255', 'unique:tenants,id', 'regex:/^[a-z0-9\-]+$/'],
            'subdomain'        => ['required', 'string', 'max:255', 'regex:/^[a-z0-9\-]+$/'],
            'admin_name'       => ['required', 'string', 'max:255'],
            'admin_email'      => ['required', 'email', 'max:255'],
            'admin_password'   => ['required', 'string', 'min:8', 'confirmed'],
            'plan'             => ['nullable', 'string', Rule::in(['basic', 'standard', 'premium'])],
            'plan_expires_at'  => ['nullable', 'date'],
            'company_name'     => ['nullable', 'string', 'max:255'],
            'contact_person'   => ['nullable', 'string', 'max:255'],
            'contact_email'    => ['nullable', 'email', 'max:255'],
            'phone'            => ['nullable', 'string', 'max:50'],
        ], [
            'id.regex'        => 'Tenant ID may only contain lowercase letters, numbers, and hyphens.',
            'subdomain.regex' => 'Subdomain may only contain lowercase letters, numbers, and hyphens.',
        ]);

        // Build the full domain from subdomain + base domain
        $fullDomain = $data['subdomain'] . '.' . $baseDomain;

        // Check domain uniqueness
        if (\Stancl\Tenancy\Database\Models\Domain::where('domain', $fullDomain)->exists()) {
            return back()
                ->withInput()
                ->withErrors(['subdomain' => "The subdomain '{$data['subdomain']}' is already taken."]);
        }

        $tenant = Tenant::create([
            'id'   => $data['id'],
            'name' => $data['company_name'] ?? $data['id'],
            'plan' => $data['plan'] ?? 'basic',
        ]);

        // Automatically create domain using base_domain
        $tenant->domains()->create([
            'domain' => $fullDomain,
        ]);

        $tenant->run(function () use ($data) {
            \Artisan::call('tenants:migrate', [
                '--tenants' => [tenant('id')],
                '--force'   => true,
            ]);

            (new \Database\Seeders\TenantAdminSeeder(
                name:     $data['admin_name'],
                email:    $data['admin_email'],
                password: $data['admin_password'],
            ))->run();
        });

        // Record initial plan history
        if (!empty($data['plan'])) {
            $planModel = Plan::where('name', $data['plan'])->first();
            if ($planModel) {
                TenantPlanHistory::create([
                    'tenant_id'    => $tenant->id,
                    'plan_id'      => $planModel->id,
                    'promotion_id' => null,
                    'price_paid'   => $planModel->base_price,
                    'starts_at'    => now(),
                    'ends_at'      => null,
                    'changed_by'   => auth()->id(),
                    'notes'        => 'Initial plan set on tenant creation.',
                ]);
            }
        }

        SuperAdminNotification::notify(
            type:    'tenant',
            title:   'New Tenant Created',
            message: "Tenant \"{$data['id']}\" was manually created. Domain: {$fullDomain}",
            icon:    'plus',
            link:    route('super_admin.tenants.show', $data['id']),
        );

        return redirect()
            ->route('super_admin.tenants.index')
            ->with('success', "Tenant \"{$tenant->id}\" created. Accessible at https://{$fullDomain}");
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('domains');

        $planHistory = TenantPlanHistory::with(['plan', 'promotion', 'changedBy'])
            ->where('tenant_id', $tenant->id)
            ->latest('starts_at')
            ->get();

        $currentPlan  = Plan::where('name', $tenant->plan ?? 'basic')->first();
        $activePromos = $currentPlan
            ? $currentPlan->activePromotions()->get()
            : collect();

        return view('super_admin.tenants.show', compact('tenant', 'planHistory', 'activePromos'));
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('domains');

        $plans       = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $currentPlan = Plan::where('name', $tenant->plan ?? '')->first();

        $activePromos = $currentPlan
            ? $currentPlan->activePromotions()->get()
            : collect();

        $expiresAt     = $tenant->plan_expires_at;
        $isExpired     = $tenant->subscriptionExpired();
        $inGrace       = $tenant->inGracePeriod();
        $daysUntil     = $tenant->daysUntilExpiry();
        $graceEndAt    = $expiresAt ? $expiresAt->copy()->addDays(7) : null;
        $graceDaysLeft = $graceEndAt ? (int) now()->diffInDays($graceEndAt, false) : null;

        return view('super_admin.tenants.edit', compact(
            'tenant', 'plans', 'activePromos', 'currentPlan',
            'expiresAt', 'isExpired', 'inGrace', 'daysUntil',
            'graceEndAt', 'graceDaysLeft'
        ));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $tenant->load('domains');

        $currentDomain = $tenant->domains->first()?->domain;

        $data = $request->validate([
            'domain' => [
                'nullable', 'string', 'max:255',
                Rule::unique('domains', 'domain')->ignore($currentDomain, 'domain'),
            ],
            'status'       => ['required', Rule::in(['active', 'inactive'])],
            'plan'         => ['nullable', 'string', Rule::in(['basic', 'standard', 'premium'])],
            'promotion_id' => ['nullable', 'integer', 'exists:plan_promotions,id'],
            'plan_notes'   => ['nullable', 'string', 'max:500'],
        ]);

        $oldStatus = $tenant->status ?? 'active';
        $oldPlan   = $tenant->plan;
        $newPlan   = $data['plan'] ?? null;

        if (!empty($data['domain'])) {
            if ($tenant->domains->first()) {
                $tenant->domains->first()->update(['domain' => $data['domain']]);
            } else {
                $tenant->domains()->create(['domain' => $data['domain']]);
            }
        }

        $tenant->update([
            'status' => $data['status'],
            'plan'   => $newPlan,
        ]);

        if ($newPlan && $oldPlan !== $newPlan) {
            $planModel = Plan::where('name', $newPlan)->first();

            if ($planModel) {
                $promo = !empty($data['promotion_id'])
                    ? PlanPromotion::find($data['promotion_id'])
                    : null;

                if ($promo && $promo->plan_id !== $planModel->id) {
                    $promo = null;
                }

                $pricePaid = $planModel->finalPrice($promo);

                TenantPlanHistory::create([
                    'tenant_id'    => $tenant->id,
                    'plan_id'      => $planModel->id,
                    'promotion_id' => $promo?->id,
                    'price_paid'   => $pricePaid,
                    'starts_at'    => now(),
                    'ends_at'      => null,
                    'changed_by'   => auth()->id(),
                    'notes'        => $data['plan_notes'] ?? null,
                ]);

                if ($promo) {
                    $promo->increment('used_count');
                }

                SuperAdminNotification::notify(
                    type:    'tenant',
                    title:   'Plan Changed',
                    message: "Tenant \"{$tenant->id}\" plan changed from " .
                             ucfirst($oldPlan ?? 'none') . " to " . ucfirst($newPlan) . ".",
                    icon:    'toggle',
                    link:    route('super_admin.tenants.show', $tenant),
                );
            }
        }

        $newStatus = $data['status'];
        if ($oldStatus !== $newStatus) {
            SuperAdminNotification::notify(
                type:    'status',
                title:   'Tenant ' . ($newStatus === 'active' ? 'Activated' : 'Deactivated'),
                message: "Tenant \"{$tenant->id}\" was " .
                         ($newStatus === 'active' ? 'activated' : 'deactivated') . ".",
                icon:    'toggle',
                link:    route('super_admin.tenants.show', $tenant),
            );
        }

        $redirectTo = $request->input('redirect_to', 'index');

        if ($redirectTo === 'show') {
            return redirect()
                ->route('super_admin.tenants.show', $tenant)
                ->with('success', "Tenant \"{$tenant->id}\" updated successfully.");
        }

        if ($redirectTo === 'edit') {
            return redirect()
                ->route('super_admin.tenants.edit', $tenant)
                ->with('success', "Tenant \"{$tenant->id}\" updated successfully.");
        }

        return redirect()
            ->route('super_admin.tenants.index')
            ->with('success', "Tenant \"{$tenant->id}\" updated successfully.");
    }

    public function destroy(Tenant $tenant)
    {
        $tenantId = $tenant->id;

        TenantPlanHistory::where('tenant_id', $tenant->id)->delete();

        TenantRegistration::where('subdomain', $tenant->id)
            ->orWhere('email', $tenant->email)
            ->delete();

        $tenant->delete();

        return redirect()
            ->route('super_admin.tenants.index')
            ->with('success', "Tenant \"{$tenantId}\" and all associated data have been deleted.");
    }
}