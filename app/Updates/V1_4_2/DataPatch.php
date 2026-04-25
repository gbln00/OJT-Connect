<?php

namespace App\Updates\V1_4_1;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataPatch
{
    public function run(Tenant $tenant): void
    {
        Log::info("[v1.4.2 DataPatch] Starting for tenant: {$tenant->id}");

        $this->backfillReadAt();
        $this->backfillSubmittedAt();
        $this->fixOrphanedApplications();
        $this->resetEvaluationDraftState();

        Log::info("[v1.4.2 DataPatch] Completed for tenant: {$tenant->id}");
    }

    // ── Patch 1 ──────────────────────────────────────────────────────
    /**
     * Backfill read_at for notifications already marked is_read = true.
     * Bug: "Read X minutes ago" was crashing because read_at was null.
     */
    private function backfillReadAt(): void
    {
        $fixed = DB::table('tenant_notifications')
            ->where('is_read', true)
            ->whereNull('read_at')
            ->update(['read_at' => DB::raw('updated_at')]);

        Log::info("[v1.4.2] Backfilled read_at for {$fixed} notifications.");
    }

    // ── Patch 2 ──────────────────────────────────────────────────────
    /**
     * Backfill submitted_at for weekly reports already submitted.
     * Bug: "Submitted on —" was showing null on all existing reports.
     */
    private function backfillSubmittedAt(): void
    {
        $fixed = DB::table('weekly_reports')
            ->whereIn('status', ['approved', 'returned'])
            ->whereNull('submitted_at')
            ->update(['submitted_at' => DB::raw('updated_at')]);

        Log::info("[v1.4.2] Backfilled submitted_at for {$fixed} weekly reports.");
    }

    // ── Patch 3 ──────────────────────────────────────────────────────
    /**
     * Fix applications where company was deleted but company_id
     * still set — causes null reference crash on applications list.
     * Bug: "Trying to get property of non-object" on app index.
     */
    private function fixOrphanedApplications(): void
    {
        $companyIds = DB::table('companies')->pluck('id');

        $fixed = DB::table('applications')
            ->whereNotNull('company_id')
            ->whereNotIn('company_id', $companyIds)
            ->update([
                'company_id'  => null,
                'updated_at'  => now(),
            ]);

        Log::info("[v1.4.2] Cleared company_id on {$fixed} orphaned applications.");
    }

    // ── Patch 4 ──────────────────────────────────────────────────────
    /**
     * Mark all existing completed evaluations as not drafts.
     * Bug: after adding is_draft column (default false), existing
     * evaluations with a score were still treated as drafts
     * in some edge-case queries.
     */
    private function resetEvaluationDraftState(): void
    {
        $fixed = DB::table('evaluations')
            ->whereNotNull('overall_grade')
            ->where('is_draft', true)
            ->update(['is_draft' => false]);

        Log::info("[v1.4.2] Reset is_draft on {$fixed} completed evaluations.");
    }
}