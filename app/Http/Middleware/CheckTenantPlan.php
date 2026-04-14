<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Plan;

class CheckTenantPlan
{
    private const LEVELS = [
        'basic'    => 1,
        'standard' => 2,
        'premium'  => 3,
    ];

    public function handle(Request $request, Closure $next, string $required): mixed
    {
        // Only applies inside a tenant context
        $tenant = tenancy()->tenant ?? null;

        if (!$tenant) {
            return $next($request);
        }

         // 1. Hard status check
        if (($tenant->status ?? 'active') === 'inactive') {
            abort(503, 'This institution\'s account has been disabled.');
        }

        // 2. Subscription expiry check
        if ($tenant->plan_expires_at) {
            if ($tenant->subscriptionExpired()) {
                $graceEnd = $tenant->plan_expires_at->copy()->addDays(7);

                if (now()->gt($graceEnd)) {
                    // Hard block — grace period exhausted
                    return response()->view('errors.subscription-expired', [
                        'tenant'       => $tenant,
                        'expiredAt'    => $tenant->plan_expires_at,
                        'graceEndedAt' => $graceEnd,
                    ], 402);
                }

                // Soft warning — still in grace window
                if (! $tenant->plan_grace) {
                    $tenant->update([
                        'plan_grace'      => true,
                        'grace_started_at' => now(),
                    ]);
                }

                session()->flash('subscription_warning', [
                    'expired_at'  => $tenant->plan_expires_at,
                    'grace_end'   => $graceEnd,
                    'days_left'   => (int) now()->diffInDays($graceEnd, false),
                ]);
            }
        }
        
        // 3. Plan level check
        $current  = strtolower($tenant->plan ?? 'basic');
        $required = strtolower($required);

        $currentLevel  = self::LEVELS[$current]  ?? 0;
        $requiredLevel = self::LEVELS[$required] ?? 99;
        
        if ($currentLevel < $requiredLevel) {
            $planModel = Plan::where('name', $required)->first();

            return response()->view('errors.plan-required', [
                'required'      => ucfirst($required),
                'current'       => ucfirst($current),
                'requiredLabel' => $planModel?->label ?? ucfirst($required),
                'currentLabel'  => Plan::where('name', $current)->first()?->label ?? ucfirst($current),
                'feature'       => $this->featureName($request->route()->getName()),
            ], 403);
        }

        return $next($request);
    }

    private function featureName(?string $routeName): string
    {
        return match(true) {
            str_contains((string) $routeName, 'export')      => 'PDF & Excel exports',
            str_contains((string) $routeName, 'reports')     => 'Weekly reports',
            str_contains((string) $routeName, 'evaluations') => 'Evaluations',
            str_contains((string) $routeName, 'analytics')   => 'Advanced analytics',
            default                                          => 'This feature',
        };
    }
}