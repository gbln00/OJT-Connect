<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    \App\Models\TenantRequestLog::where(
        'logged_at', '<', now()->subDays(90)
    )->delete();
})->dailyAt('03:00')->name('prune-tenant-logs');

Schedule::command('ojt:send-reminders')
    ->weekdays()
    ->at('08:00')
    ->name('ojt-reminders');

// Aggregate bandwidth usage for tenants daily at 12:30 AM
Schedule::command('tenants:aggregate-bandwidth --days=1')->dailyAt('00:30');

// Expire subscriptions at 1 AM daily
Schedule::command('subscriptions:expire')
    ->dailyAt('01:00')
    ->name('expire-subscriptions')
    ->withoutOverlapping();

// Send subscription reminders at 8 AM daily
Schedule::command('subscriptions:remind')
    ->dailyAt('08:00')
    ->name('subscription-reminders');

// Hour log reminders — runs daily at 8 PM
Schedule::command('ojt:send-reminders')
    ->dailyAt('20:00');

// Subscription expiry check — runs daily at midnight
Schedule::command('subscriptions:expire')
    ->daily();

// Bandwidth aggregation — runs daily at 1 AM
Schedule::command('tenants:aggregate-bandwidth')
    ->dailyAt('01:00');
