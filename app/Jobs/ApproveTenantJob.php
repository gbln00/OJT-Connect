<?php

namespace App\Jobs;

use App\Models\Tenant;
use App\Models\TenantRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ApproveTenantJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 3;
    public int $backoff = 15;

    public function __construct(
        public TenantRegistration $registration,
        public Tenant             $tenant,
        public string             $plainPassword,
    ) {}

    public function handle(): void
    {
        Log::info("[ApproveTenantJob] START for tenant: {$this->tenant->id}");

        try {
            // ── Step 1: Create tenant database and run migrations ────────
            Log::info("[ApproveTenantJob] Running migrations for: {$this->tenant->id}");

            Artisan::call('tenants:migrate', [
                '--tenants' => [$this->tenant->id],
                '--force'   => true,
            ]);

            Log::info("[ApproveTenantJob] Migration output: " . Artisan::output());

            // ── Step 2: Create admin user in tenant database ─────────────
            Log::info("[ApproveTenantJob] Seeding admin for: {$this->tenant->id}");

            $registration  = $this->registration;
            $plainPassword = $this->plainPassword;

            $this->tenant->run(function () use ($registration, $plainPassword) {
                \App\Models\User::updateOrCreate(
                    ['email' => $registration->email],
                    [
                        'name'      => $registration->contact_person,
                        'email'     => $registration->email,
                        'password'  => \Illuminate\Support\Facades\Hash::make($plainPassword),
                        'role'      => 'admin',
                        'is_active' => true,
                    ]
                );

                Log::info("[ApproveTenantJob] Admin user created: " . $registration->email);
            });

            tenancy()->end();

            Log::info("[ApproveTenantJob] DONE for tenant: {$this->tenant->id}");

        } catch (\Throwable $e) {
            Log::error("[ApproveTenantJob] FAILED for {$this->tenant->id}: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            try { tenancy()->end(); } catch (\Throwable) {}

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("[ApproveTenantJob] PERMANENTLY FAILED for {$this->tenant->id}: " . $exception->getMessage());
        try { tenancy()->end(); } catch (\Throwable) {}
    }
}