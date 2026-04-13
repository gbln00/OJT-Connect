<?php

namespace App\Listeners;

use Stancl\Tenancy\Events\TenantCreated;
use Illuminate\Support\Facades\Artisan;

class CreateTenantStorageLink
{
    public function handle(TenantCreated $event): void
    {
        tenancy()->initialize($event->tenant);
        Artisan::call('storage:link');
        tenancy()->end();
    }
}