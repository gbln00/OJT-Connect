<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessSubscriptionExpiries extends Command
{
    protected $signature   = 'subscriptions:expire';
    protected $description = 'Deactivate tenants whose grace period has ended';

    public function handle(): int
    {
        // 1. Enter grace window for newly expired subscriptions
        $enterGrace = Tenant::where('status', 'active')
            ->where('plan_grace', false)
            ->whereNotNull('plan_expires_at')
            ->where('plan_expires_at', '<', now())
            ->get();

        foreach ($enterGrace as $tenant) {
            $tenant->update([
                'plan_grace'      => true,
                'grace_started_at' => now(),
            ]);
            $this->info("Grace started: {$tenant->id}");
            // TODO: fire notification to tenant admin
        }

        // 2. Deactivate tenants where grace period (7 days) has ended
        $expired = Tenant::where('status', 'active')
            ->where('plan_grace', true)
            ->whereNotNull('grace_started_at')
            ->where('grace_started_at', '<', now()->subDays(7))
            ->get();

        foreach ($expired as $tenant) {
            $tenant->update(['status' => 'inactive']);
            $this->warn("Deactivated: {$tenant->id}");
            // TODO: fire deactivation notification
        }

        $this->info("Done. Entered grace: {$enterGrace->count()}, Deactivated: {$expired->count()}");
        return self::SUCCESS;
    }
}
