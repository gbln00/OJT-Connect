<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Runs inside each TENANT's database.
     * Do NOT use Schema::connection('mysql') here.
     */
    public function up(): void
    {
        // ── Fix 1: Add read_at to tenant_notifications ───────────────
        // Bug: "Read X minutes ago" was crashing because read_at
        // was null even for notifications with is_read = true.
        if (Schema::hasTable('tenant_notifications')
            && ! Schema::hasColumn('tenant_notifications', 'read_at')) {
            Schema::table('tenant_notifications', function (Blueprint $table) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            });
        }

        // ── Fix 2: Add submitted_at to weekly_reports ────────────────
        // Bug: "Submitted on —" showing null on all old reports
        // because the column didn't exist before this version.
        if (Schema::hasTable('weekly_reports')
            && ! Schema::hasColumn('weekly_reports', 'submitted_at')) {
            Schema::table('weekly_reports', function (Blueprint $table) {
                $table->timestamp('submitted_at')->nullable()->after('status');
            });
        }

        // ── Fix 3: Add is_draft to evaluations ───────────────────────
        // Bug: saving an evaluation mid-form submitted it immediately
        // because there was no draft state to hold it in.
        if (Schema::hasTable('evaluations')
            && ! Schema::hasColumn('evaluations', 'is_draft')) {
            Schema::table('evaluations', function (Blueprint $table) {
                $table->boolean('is_draft')->default(false)->after('remarks');
            });
        }

        // ── Fix 4: Add missing index on applications ─────────────────
        // Bug: application list was timing out for tenants with
        // 200+ records due to missing composite index.
        if (Schema::hasTable('applications')) {
            $existing = collect(
                DB::select("SHOW INDEX FROM applications
                            WHERE Key_name = 'applications_status_student_index'")
            );
            if ($existing->isEmpty()) {
                Schema::table('applications', function (Blueprint $table) {
                    $table->index(
                        ['status', 'student_id'],
                        'applications_status_student_index'
                    );
                });
            }
        }

        // ── Fix 5: Make hour_logs.time_out nullable ──────────────────
        // Bug: students who forgot to clock out caused export to
        // crash because time_out was NOT NULL with no default.
        if (Schema::hasTable('hour_logs')
            && Schema::hasColumn('hour_logs', 'time_out')) {
            Schema::table('hour_logs', function (Blueprint $table) {
                $table->time('time_out')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Reverse Fix 1
        if (Schema::hasColumn('tenant_notifications', 'read_at')) {
            Schema::table('tenant_notifications', function (Blueprint $table) {
                $table->dropColumn('read_at');
            });
        }

        // Reverse Fix 2
        if (Schema::hasColumn('weekly_reports', 'submitted_at')) {
            Schema::table('weekly_reports', function (Blueprint $table) {
                $table->dropColumn('submitted_at');
            });
        }

        // Reverse Fix 3
        if (Schema::hasColumn('evaluations', 'is_draft')) {
            Schema::table('evaluations', function (Blueprint $table) {
                $table->dropColumn('is_draft');
            });
        }

        // Note: we don't reverse Fix 4 (dropping indexes is risky)
        // and Fix 5 (keeping nullable is safe)
    }
};