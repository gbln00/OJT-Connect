<?php

namespace App\Updates\V1_4_4;

use App\Models\Tenant;
use App\Models\TenantNotification;
use App\Models\TenantSetting;
use Illuminate\Support\Facades\Log;

class DataPatch
{
    /**
     * Mock update patch: enables demo-only feature flag.
     */
    public function run(Tenant $tenant): void
    {
        TenantSetting::set('mock_feature.enabled', '1');
        TenantSetting::set('mock_feature.activated_at', now()->format('Y-m-d H:i:s'));

        TenantNotification::notify(
            title: 'Mock Feature Lab unlocked',
            message: 'v1.4.4 installed successfully. The Mock Feature Lab is now available.',
            type: 'success',
            targetRole: 'admin'
        );

        Log::info("[v1.4.4 DataPatch] Mock feature flag enabled for tenant {$tenant->id}.");
    }
}
