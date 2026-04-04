<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use App\Models\Plan;

class CoordinatorPlanController extends Controller
{
    public function index()
    {
        $tenant   = tenancy()->tenant;
        $planName = $tenant?->plan ?? 'basic';
        $plan     = Plan::where('name', $planName)->with('promotions')->first();
        $allPlans = Plan::where('is_active', true)->orderBy('sort_order')->get();
        $promos   = $plan ? $plan->activePromotions()->get() : collect();

        return view('coordinator.plan.index', compact('plan', 'allPlans', 'promos', 'planName'));
    }
}