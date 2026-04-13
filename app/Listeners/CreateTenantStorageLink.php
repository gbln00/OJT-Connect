<?php

namespace App\Listeners;

use Stancl\Tenancy\Events\TenantCreated;
use Illuminate\Support\Facades\File;

class CreateTenantStorageLink
{
    public function handle(TenantCreated $event): void
    {
        $tenantId   = $event->tenant->getTenantKey();
        $target     = storage_path("tenant{$tenantId}/app/public");
        $link       = public_path("tenant{$tenantId}");

        if (!File::exists($target)) {
            File::makeDirectory($target, 0755, true);
        }

        if (!File::exists($link)) {
            // Works on both Windows and Linux
            symlink($target, $link);
        }
    }
}