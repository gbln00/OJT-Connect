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