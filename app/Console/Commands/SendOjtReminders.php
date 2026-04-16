<?php

namespace App\Console\Commands;
use App\Models\Tenant;
use App\Models\OjtApplication;
use App\Models\HourLog; 
use App\Mail\HourLogReminder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Command;

class SendOjtReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ojt:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remind students who have not logged hours recently';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        tenancy()->runForMultiple(Tenant::all(), function() {
            $twoDaysAgo = now()->subDays(2);

            OjtApplication::where('status', 'approved')
                ->with('student')
                ->get()
                ->each(function($app) use ($twoDaysAgo) {
                    $lastLog = HourLog::where('application_id', $app->id)
                        ->latest('date')->first();

                    if (!$lastLog || $lastLog->date->lt($twoDaysAgo)) {
                        Mail::to($app->student->email)
                            ->send(new HourLogReminder($app->student, $app));
                    }
                });
        });
    }
}
