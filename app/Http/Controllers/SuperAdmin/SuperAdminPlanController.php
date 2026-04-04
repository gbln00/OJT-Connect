<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\PlanPromotion;
use Illuminate\Http\Request;

class SuperAdminPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::with('promotions')->orderBy('sort_order')->get();
        return view('super_admin.plans.index', compact('plans'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'label'       => ['required', 'string', 'max:100'],
            'base_price'  => ['required', 'integer', 'min:0'],
            'student_cap' => ['nullable', 'integer', 'min:1'],
            'features'    => ['required', 'array'],
            'is_active'   => ['boolean'],
        ]);

        $plan->update($data);

        return back()->with('success', "{$plan->label} plan updated.");
    }

    public function storePromotion(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'code'           => ['required', 'string', 'max:50', 'unique:plan_promotions,code'],
            'label'          => ['required', 'string', 'max:255'],
            'discount_type'  => ['required', 'in:percent,fixed'],
            'discount_value' => ['required', 'integer', 'min:1'],
            'starts_at'      => ['nullable', 'date'],
            'ends_at'        => ['nullable', 'date', 'after:starts_at'],
            'max_uses'       => ['nullable', 'integer', 'min:1'],
        ]);

        $plan->promotions()->create($data + ['is_active' => true]);

        return back()->with('success', "Promotion \"{$data['code']}\" created.");
    }

    public function destroyPromotion(PlanPromotion $promo)
    {
        $promo->delete();
        return back()->with('success', 'Promotion removed.');
    }

    public function togglePromotion(PlanPromotion $promo)
    {
        $promo->update(['is_active' => !$promo->is_active]);
        return back()->with('success', $promo->is_active ? 'Promotion activated.' : 'Promotion deactivated.');
    }
}