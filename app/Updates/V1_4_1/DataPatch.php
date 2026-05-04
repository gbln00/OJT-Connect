<?php

namespace App\Updates\V1_4_1;

use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DataPatch
{
    /**
     * Runs inside the tenant's database context after migrations.
     * Safe to use Eloquent models and DB queries here —
     * they will hit THIS tenant's database automatically.
     */
    public function run(Tenant $tenant): void
    {
        Log::info("[v1.4.1 DataPatch] Running for tenant: {$tenant->id}");

        $this->fixHourLogsWithNullEndedAt();
        $this->backfillSubmittedAt();
        $this->resetDraftEvaluations();
        $this->backfillReadAt();
        $this->fixOrphanedApplications();

        Log::info("[v1.4.1 DataPatch] Completed for tenant: {$tenant->id}");
    }

    // ── Patch 1 ──────────────────────────────────────────────────────
    /**
     * Hour logs that were "stuck" open (no ended_at) but were
     * created more than 24 hours ago — auto-close them at
     * 8 hours after they started, which is the standard shift.
     *
     * Bug: students who forgot to clock out left logs open forever,
     * which broke the total hours calculation on their profile.
     */
    private function fixHourLogsWithNullEndedAt(): void
    {
        $fixed = DB::table('hour_logs')
            ->whereNull('ended_at')
            ->where('created_at', '<', now()->subDay())
            ->update([
                'ended_at'   => DB::raw('DATE_ADD(started_at, INTERVAL 8 HOUR)'),
                'updated_at' => now(),
            ]);

        Log::info("[v1.4.1] Fixed {$fixed} stuck hour logs.");
    }

    // ── Patch 2 ──────────────────────────────────────────────────────
    /**
     * Backfill submitted_at for weekly reports that were already
     * submitted (status = 'submitted') but have no timestamp.
     * Use updated_at as the best approximation.
     *
     * Bug: "Submitted on —" was showing on all old reports.
     */
    private function backfillSubmittedAt(): void
    {
        $fixed = DB::table('weekly_reports')
            ->where('status', 'submitted')
            ->whereNull('submitted_at')
            ->update([
                'submitted_at' => DB::raw('updated_at'),
            ]);

        Log::info("[v1.4.1] Backfilled submitted_at for {$fixed} weekly reports.");
    }

    // ── Patch 3 ──────────────────────────────────────────────────────
    /**
     * Mark all existing evaluations as NOT drafts (is_draft = false)
     * UNLESS their score is null, which means they were never finished.
     *
     * Bug: after adding is_draft column (default true), all old
     * completed evaluations were suddenly treated as drafts and
     * disappeared from the submitted evaluations list.
     */
    private function resetDraftEvaluations(): void
    {
        // Completed evaluations — mark as submitted
        $submitted = DB::table('evaluations')
            ->whereNotNull('score')
            ->where('is_draft', true)
            ->update(['is_draft' => false]);

        // Evaluations with no score — leave as draft (correct behavior)

        Log::info("[v1.4.1] Unmarked {$submitted} evaluations from draft state.");
    }

    // ── Patch 4 ──────────────────────────────────────────────────────
    /**
     * Backfill read_at for notifications already marked is_read = true.
     * Use updated_at as approximation since that's when is_read changed.
     *
     * Bug: "Read X minutes ago" was crashing because read_at was null
     * even for notifications that had is_read = true.
     */
    private function backfillReadAt(): void
    {
        $fixed = DB::table('tenant_notifications')
            ->where('is_read', true)
            ->whereNull('read_at')
            ->update([
                'read_at' => DB::raw('updated_at'),
            ]);

        Log::info("[v1.4.1] Backfilled read_at for {$fixed} notifications.");
    }

    // ── Patch 5 ──────────────────────────────────────────────────────
    /**
     * Find applications where the company was deleted but the
     * application still has company_id set, causing a null
     * reference error on the applications list page.
     *
     * Fix: set company_id to null so the UI shows "Company removed"
     * instead of crashing.
     *
     * Bug: "Trying to get property of non-object" on applications
     * index when a company was hard-deleted by admin.
     */
    private function fixOrphanedApplications(): void
    {
        $orphaned = DB::table('applications')
            ->whereNotNull('company_id')
            ->whereNotIn('company_id', DB::table('companies')->pluck('id'))
            ->update([
                'company_id'  => null,
                'updated_at'  => now(),
            ]);

        Log::info("[v1.4.1] Cleared company_id on {$orphaned} orphaned applications.");
    }
}