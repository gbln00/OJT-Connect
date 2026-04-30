<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Evaluation;
use App\Models\HourLog;
use App\Models\OjtApplication;
use App\Models\QrClockIn;
use App\Models\StudentProfile;
use App\Models\TenantNotification;
use App\Models\TenantSetting;
use App\Models\User;
use App\Models\WeeklyReport;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->resetDemoTables();

        $admin = User::create([
            'name' => 'Tenant Admin',
            'email' => 'admin@demo.tenant',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $coordinator = User::create([
            'name' => 'Coordinator Jane',
            'email' => 'coordinator@demo.tenant',
            'password' => Hash::make('password'),
            'role' => 'ojt_coordinator',
            'is_active' => true,
        ]);

        $company = Company::create([
            'name' => 'Acme Tech Solutions',
            'address' => '123 Innovation Ave, Malaybalay City',
            'contact_person' => 'Miguel Santos',
            'contact_email' => 'hr@acmetech.demo',
            'contact_phone' => '09170000001',
            'industry' => 'Information Technology',
            'is_active' => true,
        ]);

        $supervisor = User::create([
            'name' => 'Supervisor Mark',
            'email' => 'supervisor@demo.tenant',
            'password' => Hash::make('password'),
            'role' => 'company_supervisor',
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        $studentA = User::create([
            'name' => 'Student Alpha',
            'email' => 'student.alpha@demo.tenant',
            'password' => Hash::make('password'),
            'role' => 'student_intern',
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        $studentB = User::create([
            'name' => 'Student Beta',
            'email' => 'student.beta@demo.tenant',
            'password' => Hash::make('password'),
            'role' => 'student_intern',
            'company_id' => $company->id,
            'is_active' => true,
        ]);

        StudentProfile::create([
            'user_id' => $studentA->id,
            'student_id' => '2026-0001',
            'firstname' => 'Student',
            'lastname' => 'Alpha',
            'middlename' => 'A',
            'course' => 'BS Information Technology',
            'year_level' => '4th Year',
            'section' => 'IT-4A',
            'phone' => '09170000002',
            'address' => 'Malaybalay City',
            'required_hours' => 490,
        ]);

        StudentProfile::create([
            'user_id' => $studentB->id,
            'student_id' => '2026-0002',
            'firstname' => 'Student',
            'lastname' => 'Beta',
            'middlename' => 'B',
            'course' => 'BS Information Technology',
            'year_level' => '4th Year',
            'section' => 'IT-4B',
            'phone' => '09170000003',
            'address' => 'Valencia City',
            'required_hours' => 600,
        ]);

        $approvedApplication = OjtApplication::create([
            'student_id' => $studentA->id,
            'company_id' => $company->id,
            'reviewed_by' => $coordinator->id,
            'program' => 'BS Information Technology',
            'school_year' => '2026-2027',
            'semester' => '1st',
            'required_hours' => 490,
            'document_path' => 'demo/application-alpha.pdf',
            'status' => 'approved',
            'remarks' => 'Approved for internship placement.',
            'reviewed_at' => now()->subDays(25),
        ]);

        $pendingApplication = OjtApplication::create([
            'student_id' => $studentB->id,
            'company_id' => $company->id,
            'reviewed_by' => null,
            'program' => 'BS Information Technology',
            'school_year' => '2026-2027',
            'semester' => '1st',
            'required_hours' => 600,
            'document_path' => 'demo/application-beta.pdf',
            'status' => 'pending',
            'remarks' => 'Awaiting coordinator review.',
            'reviewed_at' => null,
        ]);

        $startDate = Carbon::now()->subWeeks(5)->startOfWeek();

        for ($i = 0; $i < 10; $i++) {
            $logDate = $startDate->copy()->addDays($i);
            HourLog::create([
                'student_id' => $studentA->id,
                'application_id' => $approvedApplication->id,
                'date' => $logDate->toDateString(),
                'session' => $i % 2 === 0 ? 'morning' : 'afternoon',
                'time_in' => '08:00:00',
                'time_out' => '12:00:00',
                'total_hours' => 4,
                'description' => 'Demo task log #' . ($i + 1),
                'status' => $i < 8 ? 'approved' : 'pending',
                'approved_by' => $i < 8 ? $supervisor->id : null,
                'approved_at' => $i < 8 ? $logDate->copy()->setTime(17, 0, 0) : null,
                'rejection_reason' => null,
            ]);
        }

        for ($week = 1; $week <= 3; $week++) {
            $weekStart = $startDate->copy()->addWeeks($week - 1);
            $isReviewed = $week <= 2;

            WeeklyReport::create([
                'student_id' => $studentA->id,
                'application_id' => $approvedApplication->id,
                'week_number' => $week,
                'week_start' => $weekStart->toDateString(),
                'week_end' => $weekStart->copy()->addDays(6)->toDateString(),
                'description' => "Weekly accomplishments report for week {$week}.",
                'file_path' => "demo/reports/week-{$week}.pdf",
                'status' => $isReviewed ? 'approved' : 'pending',
                'feedback' => $isReviewed ? 'Good progress and clear documentation.' : null,
                'reviewed_by' => $isReviewed ? $coordinator->id : null,
                'reviewed_at' => $isReviewed ? $weekStart->copy()->addDays(7) : null,
            ]);
        }

        Evaluation::create([
            'student_id' => $studentA->id,
            'application_id' => $approvedApplication->id,
            'supervisor_id' => $supervisor->id,
            'attendance_rating' => 5,
            'performance_rating' => 4,
            'overall_grade' => 92.5,
            'recommendation' => 'pass',
            'remarks' => 'Consistent performance and strong communication.',
            'submitted_at' => now()->subDays(3),
        ]);

        QrClockIn::create([
            'company_id' => $company->id,
            'supervisor_id' => $supervisor->id,
            'token' => QrClockIn::generateToken(),
            'is_active' => true,
        ]);

        TenantSetting::set('branding.app_name', 'OJTConnect Demo Tenant');
        TenantSetting::set('branding.primary_color', '#8C0E03');
        TenantSetting::set('dashboard.welcome_message', 'Welcome to the demo tenant environment.');
        TenantSetting::set('analytics.default_range_days', '30');

        TenantNotification::create([
            'type' => 'info',
            'title' => 'Demo tenant ready',
            'message' => 'All feature modules have sample data for walkthrough.',
            'target_role' => 'admin',
            'user_id' => $admin->id,
            'is_read' => false,
        ]);

        TenantNotification::create([
            'type' => 'approval',
            'title' => 'Pending application',
            'message' => "{$studentB->name} submitted a new OJT application.",
            'target_role' => 'ojt_coordinator',
            'user_id' => $coordinator->id,
            'is_read' => false,
        ]);

        TenantNotification::create([
            'type' => 'info',
            'title' => 'Hour logs awaiting review',
            'message' => 'Two hour logs are pending your supervisor approval.',
            'target_role' => 'company_supervisor',
            'user_id' => $supervisor->id,
            'is_read' => false,
        ]);

        TenantNotification::create([
            'type' => 'success',
            'title' => 'Evaluation submitted',
            'message' => 'Your supervisor submitted your final evaluation.',
            'target_role' => 'student_intern',
            'user_id' => $studentA->id,
            'is_read' => false,
        ]);

        $this->command?->info('Tenant demo seed complete.');
        $this->command?->line('Demo login password for all users: password');
        $this->command?->table(
            ['Role', 'Email'],
            [
                ['Admin', $admin->email],
                ['Coordinator', $coordinator->email],
                ['Supervisor', $supervisor->email],
                ['Student', $studentA->email],
                ['Student', $studentB->email],
            ]
        );
    }

    private function resetDemoTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'qr_clock_ins',
            'evaluations',
            'weekly_reports',
            'hour_logs',
            'applications',
            'student_profiles',
            'tenant_notifications',
            'tenant_settings',
            'companies',
            'users',
        ] as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
