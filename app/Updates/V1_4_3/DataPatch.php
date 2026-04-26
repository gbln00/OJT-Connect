<?php

namespace App\Updates\V1_4_3;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class DataPatch
{
    /**
     * Runs inside the tenant's database context after migrations.
     *
     * v1.4.3 introduces the support_tickets and support_ticket_replies
     * tables from scratch — there is no existing data to backfill.
     * This patch simply verifies the tables exist and logs the result,
     * making it easy to spot failed installs in the log trail.
     */
    public function run(Tenant $tenant): void
    {
        Log::info("[v1.4.3 DataPatch] Starting for tenant: {$tenant->id}");

        $this->verifyTables($tenant);

        Log::info("[v1.4.3 DataPatch] Completed for tenant: {$tenant->id}");
    }

    // ── Patch 1 ──────────────────────────────────────────────────────
    /**
     * Confirm that both support tables were created by the migration.
     * Logs a warning (not an exception) if either is missing so the
     * install job can still complete — the tables might have been
     * created manually on older installs.
     */
    private function verifyTables(Tenant $tenant): void
    {
        $tables = ['support_tickets', 'support_ticket_replies'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                Log::info("[v1.4.3] Table '{$table}' confirmed for tenant {$tenant->id}.");
            } else {
                Log::warning("[v1.4.3] Table '{$table}' NOT found for tenant {$tenant->id} — migration may have failed.");
            }
        }
    }
}
