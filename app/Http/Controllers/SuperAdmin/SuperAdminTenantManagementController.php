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
        $plans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        return view('super_admin.tenants.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id'             => ['required', 'string', 'max:255', 'unique:tenants,id', 'regex:/^[a-z0-9\-]+$/'],
            'domain'         => ['required', 'string', 'max:255', 'unique:domains,domain'],
            'admin_name'     => ['required', 'string', 'max:255'],
            'admin_email'    => ['required', 'email', 'max:255'],
            'admin_password' => ['required', 'string', 'min:8'],
            'plan'           => ['nullable', 'string', Rule::in(['basic', 'standard', 'premium'])],
        ], [
            'id.regex' => 'Tenant ID may only contain lowercase letters, numbers, and hyphens.',
        ]);

        $tenant = Tenant::create([
            'id'   => $data['id'],
            'plan' => $data['plan'] ?? 'basic',
        ]);

        $$tenant->domains()->create([
            'domain' => $registration->subdomain . '.' . config('app.base_domain'),
        ]);

        $tenant->run(function () use ($data) {
            \Artisan::call('tenants:migrate', [
                '--tenants' => [tenant('id')],
                '--force'   => true,
            ]);

            (new \Database\Seeders\TenantAdminSeeder)->run(
                name:     $data['admin_name'],
                email:    $data['admin_email'],
                password: $data['admin_password'],
            );
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
            message: "Tenant \"{$data['id']}\" was manually created by super admin.",
            icon:    'plus',
            link:    route('super_admin.tenants.show', $data['id']),
        );

        return redirect()
            ->route('super_admin.tenants.index')
            ->with('success', "Tenant \"{$tenant->id}\" created successfully.");
    }

    public function show(Tenant $tenant)
    {
        $tenant->load('domains');

        // Plan history from central DB — no tenancy needed
        $planHistory = TenantPlanHistory::with(['plan', 'promotion', 'changedBy'])
            ->where('tenant_id', $tenant->id)
            ->latest('starts_at')
            ->get();

        // Active promos for this tenant's plan (for the quick-assign form)
        $currentPlan    = Plan::where('name', $tenant->plan ?? 'basic')->first();
        $activePromos   = $currentPlan
            ? $currentPlan->activePromotions()->get()
            : collect();

        return view('super_admin.tenants.show', compact('tenant', 'planHistory', 'activePromos'));
    }

    public function edit(Tenant $tenant)
    {
        $tenant->load('domains');

        $plans       = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $currentPlan = Plan::where('name', $tenant->plan ?? '')->first();

        // Only show promos for the currently selected plan
        $activePromos = $currentPlan
            ? $currentPlan->activePromotions()->get()
            : collect();

        return view('super_admin.tenants.edit', compact(
            'tenant', 'plans', 'activePromos', 'currentPlan'
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

        // Update domain if submitted
        if (!empty($data['domain'])) {
            if ($tenant->domains->first()) {
                $tenant->domains->first()->update(['domain' => $data['domain']]);
            } else {
                $tenant->domains()->create(['domain' => $data['domain']]);
            }
        }

        // Update tenant columns
        $tenant->update([
            'status' => $data['status'],
            'plan'   => $newPlan,
        ]);

        // Record plan history when plan actually changes
        if ($newPlan && $oldPlan !== $newPlan) {
            $planModel = Plan::where('name', $newPlan)->first();

            if ($planModel) {
                $promo = !empty($data['promotion_id'])
                    ? PlanPromotion::find($data['promotion_id'])
                    : null;

                // Validate promo belongs to this plan
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

                // Increment promo usage counter
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

        // Status change notification
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

        // Clean up plan history and registration records
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