<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Events\TenantCreated;
use Illuminate\Support\Facades\File;
use Throwable;

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
            $this->createLink($target, $link, $tenantId);
        }
    }

    private function createLink(string $target, string $link, string $tenantId): void
    {
        try {
            if (DIRECTORY_SEPARATOR === '\\') {
                $this->createWindowsJunction($target, $link);
                return;
            }

            symlink($target, $link);
        } catch (Throwable $e) {
            // Don't block tenant provisioning if symlink/junction creation is not allowed.
            Log::warning('Unable to create tenant storage link.', [
                'tenant_id' => $tenantId,
                'target' => $target,
                'link' => $link,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function createWindowsJunction(string $target, string $link): void
    {
        $targetEscaped = str_replace('"', '\"', $target);
        $linkEscaped = str_replace('"', '\"', $link);

        exec("cmd /c mklink /J \"{$linkEscaped}\" \"{$targetEscaped}\"", $output, $exitCode);

        if ($exitCode !== 0 || !File::exists($link)) {
            throw new \RuntimeException('Failed to create Windows junction for tenant storage.');
        }
    }
}