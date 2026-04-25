<?php
namespace App\Updates\V1_3_0;

use App\Models\Tenant;

class DataPatch
{
    /**
     * Runs inside tenant context.
     * Safe to use tenant-scoped models here.
     */
    public function run(Tenant $tenant): void
    {
        // Example: seed a new default config value
        \App\Models\TenantSetting::firstOrCreate(
            ['key' => 'new_feature_enabled'],
            ['value' => '1']
        );

        // Example: migrate existing data to new column format
        // \DB::table('applications')->whereNull('new_column')->update(['new_column' => 'default']);
    }
}