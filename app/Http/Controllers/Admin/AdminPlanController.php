<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;

class AdminPlanController extends Controller
{
    /**
     * Show the current plan details and available promotions
     * for the authenticated tenant admin.
     */
    public function index()
    {
        $tenant   = tenancy()->tenant;
        $planName = $tenant?->plan ?? 'basic';
        $plan     = Plan::where('name', $planName)->with('promotions')->first();
        $allPlans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $promos   = $plan ? $plan->activePromotions()->get() : collect();

        return view('admin.plan.index', compact('plan', 'allPlans', 'promos', 'planName'));
    }
}