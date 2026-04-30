<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanPromotion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuperAdminPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with('promotions')->orderBy('sort_order')->get();
        return view('super_admin.plans.index', compact('plans'));
    }

    /**
     * Show create plan form
     */
    public function create()
    {
        return view('super_admin.plans.create');
    }

    /**
     * Store a new plan
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:50', 'unique:plans,name', 'regex:/^[a-z0-9_]+$/'],
            'label'        => ['required', 'string', 'max:100'],
            'base_price'   => ['required', 'integer', 'min:0'],
            'billing_cycle'=> ['required', 'string', 'in:monthly,yearly'],
            'student_cap'  => ['nullable', 'integer', 'min:1'],
            'features'     => ['nullable', 'array'],
            'is_active'    => ['boolean'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'renewal_date' => ['nullable', 'date'],
            'description'  => ['nullable', 'string', 'max:500'],
        ], [
            'name.regex' => 'Plan key may only contain lowercase letters, numbers, and underscores.',
        ]);

        // Build features array with all keys defaulting to false
        $allFeatureKeys = [
            'hour_logs',
            'weekly_reports',
            'evaluations',
            'csv_import',
            'pdf_export',
            'excel_export',
            'analytics_dashboard',
            'tenant_customization',
            'qr_clock_in',
            'email_notifs',
        ];
        $features = [];
        foreach ($allFeatureKeys as $key) {
            $features[$key] = !empty($data['features'][$key]);
        }

        Plan::create([
            'name'          => $data['name'],
            'label'         => $data['label'],
            'base_price'    => $data['base_price'],
            'billing_cycle' => $data['billing_cycle'],
            'student_cap'   => $data['student_cap'] ?? null,
            'features'      => $features,
            'is_active'     => $request->boolean('is_active'),
            'sort_order'    => $data['sort_order'] ?? Plan::max('sort_order') + 1,
            'renewal_date'  => $data['renewal_date'] ?? null,
            'description'   => $data['description'] ?? null,
        ]);

        return redirect()
            ->route('super_admin.plans.index')
            ->with('success', "Plan \"{$data['label']}\" created successfully.");
    }

    /**
     * Show edit form for a single plan
     */
    public function edit(Plan $plan)
    {
        $plan->load('promotions');
        return view('super_admin.plans.edit', compact('plan'));
    }

    /**
     * Update an existing plan
     */
    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'label'        => ['required', 'string', 'max:100'],
            'base_price'   => ['required', 'integer', 'min:0'],
            'billing_cycle'=> ['required', 'string', 'in:monthly,yearly'],
            'student_cap'  => ['nullable', 'integer', 'min:1'],
            'features'     => ['nullable', 'array'],
            'is_active'    => ['boolean'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'renewal_date' => ['nullable', 'date'],
            'description'  => ['nullable', 'string', 'max:500'],
        ]);

        $allFeatureKeys = [
            'hour_logs',
            'weekly_reports',
            'evaluations',
            'csv_import',
            'pdf_export',
            'excel_export',
            'analytics_dashboard',
            'tenant_customization',
            'qr_clock_in',
            'email_notifs',
        ];
        $features = [];
        foreach ($allFeatureKeys as $key) {
            $features[$key] = !empty($data['features'][$key]);
        }

        $plan->update([
            'label'         => $data['label'],
            'base_price'    => $data['base_price'],
            'billing_cycle' => $data['billing_cycle'] ?? 'yearly',
            'student_cap'   => $data['student_cap'] ?? null,
            'features'      => $features,
            'is_active'     => $request->boolean('is_active'),
            'sort_order'    => $data['sort_order'] ?? $plan->sort_order,
            'renewal_date'  => $data['renewal_date'] ?? null,
            'description'   => $data['description'] ?? null,
        ]);

        return back()->with('success', "{$plan->label} plan updated.");
    }

    /**
     * Delete a plan (only if no tenants are on it)
     */
    public function destroy(Plan $plan)
    {
        // Check if any tenants are currently on this plan
        $tenantCount = \App\Models\Tenant::where('plan', $plan->name)->count();

        if ($tenantCount > 0) {
            return back()->with('error', "Cannot delete \"{$plan->label}\" — {$tenantCount} tenant(s) are currently on this plan. Reassign them first.");
        }

        $label = $plan->label;

        // Delete all promotions first
        $plan->promotions()->delete();
        $plan->delete();

        return redirect()
            ->route('super_admin.plans.index')
            ->with('success', "Plan \"{$label}\" deleted successfully.");
    }

    /**
     * Toggle plan active status
     */
    public function toggle(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);

        return back()->with('success', $plan->is_active
            ? "\"{$plan->label}\" plan activated."
            : "\"{$plan->label}\" plan deactivated.");
    }

    // ── Promotions ──────────────────────────────────────────────────────

    public function storePromotion(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'code'           => ['required', 'string', 'max:50', 'unique:plan_promotions,code'],
            'label'          => ['required', 'string', 'max:255'],
            'discount_type'  => ['required', 'in:percent,fixed'],
            'discount_value' => ['required', 'integer', 'min:1'],
            'starts_at'      => ['nullable', 'date'],
            'ends_at'        => ['nullable', 'date', 'after_or_equal:starts_at'],
            'max_uses'       => ['nullable', 'integer', 'min:1'],
        ]);

        $plan->promotions()->create($data + ['is_active' => true, 'used_count' => 0]);

        return back()->with('success', "Promotion \"{$data['code']}\" created.");
    }

    public function updatePromotion(Request $request, PlanPromotion $promo)
    {
        $data = $request->validate([
            'code'           => ['required', 'string', 'max:50', Rule::unique('plan_promotions', 'code')->ignore($promo->id)],
            'label'          => ['required', 'string', 'max:255'],
            'discount_type'  => ['required', 'in:percent,fixed'],
            'discount_value' => ['required', 'integer', 'min:1'],
            'starts_at'      => ['nullable', 'date'],
            'ends_at'        => ['nullable', 'date', 'after_or_equal:starts_at'],
            'max_uses'       => ['nullable', 'integer', 'min:1'],
        ]);

        $promo->update($data);

        return back()->with('success', "Promotion \"{$promo->code}\" updated.");
    }

    public function destroyPromotion(PlanPromotion $promo)
    {
        $code = $promo->code;
        $promo->delete();
        return back()->with('success', "Promotion \"{$code}\" removed.");
    }

    public function togglePromotion(PlanPromotion $promo)
    {
        $promo->update(['is_active' => !$promo->is_active]);
        return back()->with('success', $promo->is_active ? 'Promotion activated.' : 'Promotion deactivated.');
    }
}