<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionWarning
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $tenant = tenancy()->tenant ?? null;

        if ($tenant && $tenant->plan_expires_at) {
            $inGrace   = $tenant->inGracePeriod();
            $daysUntil = $tenant->days_until_expiry;
            $expiresAt = $tenant->plan_expires_at;
            $graceEnd  = $expiresAt->copy()->addDays(7);

            if ($inGrace || ($daysUntil !== null && $daysUntil <= 30)) {
                session()->flash('subscription_warning', [
                    'expired_at' => $expiresAt,
                    'grace_end'  => $graceEnd,
                    'days_left'  => (int) now()->diffInDays($graceEnd, false),
                ]);
            }
        }

        return $next($request);
    }
}
