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
        public string $plainPassword,
    ) {}

    public function handle(): void
    {
        try {
            $this->tenant->run(function () {
                Artisan::call('tenants:migrate', [
                    '--tenants' => [tenant('id')],
                    '--force'   => true,
                ]);
    
                (new \Database\Seeders\TenantAdminSeeder(
                    name:     $this->registration->contact_person,
                    email:    $this->registration->email,
                    password: $this->plainPassword,
                ))->run();
            });
    
            tenancy()->end();
            Log::info("Tenant {$this->tenant->id} provisioned successfully.");
    
        } catch (\Throwable $e) {
            Log::error("ApproveTenantJob failed: " . $e->getMessage());
            tenancy()->end();
            throw $e;
        }
    }
}