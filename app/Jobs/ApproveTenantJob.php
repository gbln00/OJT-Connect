<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\TenantRegistration;
use App\Mail\TenantApproved;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ApproveTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries = 1;

    public function __construct(
        public TenantRegistration $registration,
        public Tenant $tenant,
    ) {}

    public function handle(): void
    {
        $plainPassword = Str::password(12);
    
        try {
            // Run migrations and seed inside tenant context
            $this->tenant->run(function () use ($plainPassword) {
                Artisan::call('tenants:migrate', [
                    '--tenants' => [tenant('id')],
                    '--force'   => true,
                ]);
    
                (new \Database\Seeders\TenantAdminSeeder(
                    name:     $this->registration->contact_person,
                    email:    $this->registration->email,
                    password: $plainPassword,
                ))->run();
            });
    
            // Send email OUTSIDE tenant context
            tenancy()->end();
    
            Mail::to($this->registration->email)
                ->send(new TenantApproved($this->registration, $plainPassword));
    
            Log::info("Tenant approval email sent to {$this->registration->email}");
    
        } catch (\Throwable $e) {
            Log::error("ApproveTenantJob failed: " . $e->getMessage());
            tenancy()->end();
            throw $e;
        }
    }
}