<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * This migration runs inside EACH TENANT'S database context.
     * Do NOT use Schema::connection('mysql') here — that targets the
     * central DB. Leave connection blank so it uses the tenant DB.
     */
    public function up(): void
    {
        // ── Fix 1: hour_logs missing nullable on ended_at ────────────
        // Bug: clocking in without clocking out crashed the export
        // because ended_at was NOT NULL but had no default.
        if (Schema::hasColumn('hour_logs', 'ended_at')) {
            Schema::table('hour_logs', function (Blueprint $table) {
                $table->timestamp('ended_at')->nullable()->change();
            });
        }

        // ── Fix 2: applications missing index on status + student_id ─
        // Bug: application list page was timing out for tenants with
        // 500+ applications because there was no composite index.
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                // Check if index doesn't already exist before adding
                $indexes = collect(
                    DB::select("SHOW INDEX FROM applications WHERE Key_name = 'applications_status_student_index'")
                );
                if ($indexes->isEmpty()) {
                    $table->index(['status', 'student_id'], 'applications_status_student_index');
                }
            });
        }

        // ── Fix 3: weekly_reports add missing submitted_at column ────
        // Bug: reports submitted before v1.4.0 had no submitted_at
        // timestamp, causing the "submitted on" label to show null.
        if (Schema::hasTable('weekly_reports') && ! Schema::hasColumn('weekly_reports', 'submitted_at')) {
            Schema::table('weekly_reports', function (Blueprint $table) {
                $table->timestamp('submitted_at')->nullable()->after('status');
            });
        }

        // ── Fix 4: evaluations add is_draft column ───────────────────
        // Bug: evaluations had no draft state — saving mid-form
        // would mark it as submitted immediately.
        if (Schema::hasTable('evaluations') && ! Schema::hasColumn('evaluations', 'is_draft')) {
            Schema::table('evaluations', function (Blueprint $table) {
                $table->boolean('is_draft')->default(true)->after('score');
            });
        }

        // ── Fix 5: notifications add read_at column ──────────────────
        // Bug: is_read boolean couldn't tell WHEN it was read,
        // breaking the "read X minutes ago" display.
        if (Schema::hasTable('tenant_notifications') && ! Schema::hasColumn('tenant_notifications', 'read_at')) {
            Schema::table('tenant_notifications', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            });
        }
    }

    public function down(): void
    {
        // Reverse Fix 3
        if (Schema::hasColumn('weekly_reports', 'submitted_at')) {
            Schema::table('weekly_reports', function (Blueprint $table) {
                $table->dropColumn('submitted_at');
            });
        }

        // Reverse Fix 4
        if (Schema::hasColumn('evaluations', 'is_draft')) {
            Schema::table('evaluations', function (Blueprint $table) {
                $table->dropColumn('is_draft');
            });
        }

        // Reverse Fix 5
        if (Schema::hasColumn('tenant_notifications', 'read_at')) {
            Schema::table('tenant_notifications', function (Blueprint $table) {
                $table->dropColumn('read_at');
            });
        }

        // Note: we don't reverse Fix 1 (making nullable is safe to keep)
        // and Fix 2 (dropping indexes is risky, leave it)
    }
};