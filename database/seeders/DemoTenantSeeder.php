<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\SystemVersion;
use App\Models\TenantUpdate;
use Carbon\Carbon;

/**
 * DemoTenantSeeder — Fully populated demo tenant for presentations.
 *
 * Safe to re-run: uses updateOrCreate / firstOrCreate throughout.
 *
 * Credentials:
 *   URL:         https://demo.ojt-connect.xyz
 *   Admin:       admin@demo.com       / Demo@Admin123
 *   Coordinator: coordinator@demo.com / Demo@123
 *   Supervisor:  supervisor@demo.com  / Demo@123
 *   Student 1:   juan@demo.com        / Demo@123  (80% done, in progress)
 *   Student 2:   maria@demo.com       / Demo@123  (100% done, evaluated PASS)
 *   Student 3:   pedro@demo.com       / Demo@123  (25% done, early stage)
 *   Student 4:   liza@demo.com        / Demo@123  (application pending)
 *   Student 5:   carlo@demo.com       / Demo@123  (application rejected)
 */
class DemoTenantSeeder extends Seeder
{
    const TENANT_ID     = 'demo';
    const TENANT_DOMAIN = 'demo.ojt-connect.xyz';
    const TENANT_NAME   = 'Bukidnon State University — College of Technology';
    const TENANT_PLAN   = 'premium';

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('── DemoTenantSeeder ─────────────────────────────────');

        // ── 1. Create or fetch the tenant ─────────────────────────────
        $tenant = Tenant::find(self::TENANT_ID);

        if ($tenant) {
            $this->command->warn('  Demo tenant already exists — refreshing data only.');
        } else {
            $tenant = Tenant::create([
                'id'              => self::TENANT_ID,
                'name'            => self::TENANT_NAME,
                'plan'            => self::TENANT_PLAN,
                'status'          => 'active',
                'plan_expires_at' => now()->addYear(),
            ]);

            $tenant->domains()->firstOrCreate([
                'domain' => self::TENANT_DOMAIN,
            ]);

            $this->command->info('  ✓ Tenant created: ' . self::TENANT_DOMAIN);
        }

        // ── 2. Migrate tenant DB if needed ────────────────────────────
        \Illuminate\Support\Facades\Artisan::call('tenants:migrate', [
            '--tenants' => [self::TENANT_ID],
            '--force'   => true,
        ]);
        $this->command->info('  ✓ Migrations run');

        // ── 3. Seed all data inside tenant context ────────────────────
        $tenant->run(function () {
            $this->seedSettings();
            $users      = $this->seedUsers();
            $companies  = $this->seedCompanies($users['supervisor']);
            $this->seedApplicationsAndHours($users, $companies);
            $this->seedWeeklyReports($users, $companies);
            $this->seedEvaluations($users, $companies);
            $this->seedQrClockIn($users['supervisor'], $companies['innovate']);
            $this->seedSupportTickets($users);
            $this->seedNotifications($users);
        });

        // ── 4. TenantUpdate rows for testing the Install button ───────
        $this->seedTenantUpdates($tenant);

        $this->command->info('');
        $this->command->info('  ✓ Demo tenant fully seeded!');
        $this->command->info('  URL:         https://' . self::TENANT_DOMAIN);
        $this->command->info('  Admin:       admin@demo.com / Demo@Admin123');
        $this->command->info('  Coordinator: coordinator@demo.com / Demo@123');
        $this->command->info('  Supervisor:  supervisor@demo.com / Demo@123');
        $this->command->info('  Students:    juan, maria, pedro, liza, carlo @demo.com / Demo@123');
        $this->command->info('────────────────────────────────────────────────────');
    }

    // ──────────────────────────────────────────────────────────────────
    // Tenant Settings (branding for customization demo)
    // ──────────────────────────────────────────────────────────────────
    private function seedSettings(): void
    {
        $settings = [
            'brand_name'              => 'BukSU — College of Technology',
            'brand_color'             => '8C0E03',
            'brand_color_secondary'   => '0E1126',
            'brand_font'              => 'poppins',
            'email_greeting'          => 'Dear OJT Student,',
            'email_signature'         => 'OJT Office, College of Technology',
            'ojt_required_hours'      => '486',
            'ojt_passing_grade'       => '75',
            'announcement_text'       => '📢 OJT Demo Mode — All data is for demonstration purposes only.',
            'announcement_active'     => '1',
            'session_morning_start'   => '08:00',
            'session_morning_end'     => '12:00',
            'session_afternoon_start' => '13:00',
            'session_afternoon_end'   => '17:00',
            'require_log_description' => '0',
            'allow_edit_rejected'     => '1',
        ];

        foreach ($settings as $key => $value) {
            \App\Models\TenantSetting::set($key, $value);
        }

        $this->command->info('  ✓ Tenant settings seeded');
    }

    // ──────────────────────────────────────────────────────────────────
    // Users
    // ──────────────────────────────────────────────────────────────────
    private function seedUsers(): array
    {
        $password = Hash::make('Demo@123');

        $admin = \App\Models\User::updateOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name'      => 'Demo Admin',
                'password'  => Hash::make('Demo@Admin123'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        $coordinator = \App\Models\User::updateOrCreate(
            ['email' => 'coordinator@demo.com'],
            [
                'name'      => 'Ana Santos',
                'password'  => $password,
                'role'      => 'ojt_coordinator',
                'is_active' => true,
            ]
        );

        // Supervisor will be linked to company after company is created
        $supervisor = \App\Models\User::updateOrCreate(
            ['email' => 'supervisor@demo.com'],
            [
                'name'      => 'Robert Cruz',
                'password'  => $password,
                'role'      => 'company_supervisor',
                'is_active' => true,
            ]
        );

        // Supervisor 2 for second company
        $supervisor2 = \App\Models\User::updateOrCreate(
            ['email' => 'supervisor2@demo.com'],
            [
                'name'      => 'Sofia Mendez',
                'password'  => $password,
                'role'      => 'company_supervisor',
                'is_active' => true,
            ]
        );

        // Students
        $studentData = [
            'juan' => [
                'name' => 'Juan dela Cruz', 'email' => 'juan@demo.com',
                'student_id' => '2021-00101', 'course' => 'BS Information Technology',
                'year_level' => '4th Year', 'section' => '4A',
                'phone' => '09171234501', 'address' => 'Cagayan de Oro City',
            ],
            'maria' => [
                'name' => 'Maria Reyes', 'email' => 'maria@demo.com',
                'student_id' => '2021-00102', 'course' => 'BS Computer Science',
                'year_level' => '4th Year', 'section' => '4B',
                'phone' => '09171234502', 'address' => 'Iligan City',
            ],
            'pedro' => [
                'name' => 'Pedro Garcia', 'email' => 'pedro@demo.com',
                'student_id' => '2021-00103', 'course' => 'BS Information Technology',
                'year_level' => '4th Year', 'section' => '4A',
                'phone' => '09171234503', 'address' => 'Malaybalay City',
            ],
            'liza' => [
                'name' => 'Liza Fernandez', 'email' => 'liza@demo.com',
                'student_id' => '2021-00104', 'course' => 'BS Electronics',
                'year_level' => '4th Year', 'section' => '4C',
                'phone' => '09171234504', 'address' => 'Valencia City',
            ],
            'carlo' => [
                'name' => 'Carlo Bautista', 'email' => 'carlo@demo.com',
                'student_id' => '2021-00105', 'course' => 'BS Food Technology',
                'year_level' => '4th Year', 'section' => '4D',
                'phone' => '09171234505', 'address' => 'Butuan City',
            ],
        ];

        $students = [];
        foreach ($studentData as $key => $data) {
            $user = \App\Models\User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'],
                    'password'  => $password,
                    'role'      => 'student_intern',
                    'is_active' => true,
                ]
            );

            // Create student profile
            \App\Models\StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'student_id'     => $data['student_id'],
                    'firstname'      => explode(' ', $data['name'])[0],
                    'lastname'       => implode(' ', array_slice(explode(' ', $data['name']), 1)),
                    'course'         => $data['course'],
                    'year_level'     => $data['year_level'],
                    'section'        => $data['section'],
                    'required_hours' => 486,
                    'phone'          => $data['phone'],
                    'address'        => $data['address'],
                ]
            );

            $students[$key] = $user;
        }

        $this->command->info('  ✓ Users + student profiles seeded (9 users)');

        return compact('admin', 'coordinator', 'supervisor', 'supervisor2', 'students');
    }

    // ──────────────────────────────────────────────────────────────────
    // Companies
    // ──────────────────────────────────────────────────────────────────
    private function seedCompanies(\App\Models\User $supervisor): array
    {
        $innovate = \App\Models\Company::updateOrCreate(
            ['name' => 'Innovate Tech Solutions'],
            [
                'industry'       => 'Information Technology',
                'address'        => 'Cagayan de Oro City, Misamis Oriental',
                'contact_person' => 'Robert Cruz',
                'contact_email'  => 'hr@innovatetech.demo',
                'contact_phone'  => '09171234567',
                'is_active'      => true,
            ]
        );

        $greenleaf = \App\Models\Company::updateOrCreate(
            ['name' => 'GreenLeaf Digital'],
            [
                'industry'       => 'Digital Marketing',
                'address'        => 'Iligan City, Lanao del Norte',
                'contact_person' => 'Sofia Mendez',
                'contact_email'  => 'careers@greenleaf.demo',
                'contact_phone'  => '09281234567',
                'is_active'      => true,
            ]
        );

        // Also create a third inactive company to show toggle feature
        \App\Models\Company::updateOrCreate(
            ['name' => 'Mindanao BPO Corp'],
            [
                'industry'       => 'Business Process Outsourcing',
                'address'        => 'Davao City',
                'contact_person' => 'Mark Villanueva',
                'contact_email'  => 'hr@mindanaobpo.demo',
                'contact_phone'  => '09381234567',
                'is_active'      => false,
            ]
        );

        // Link supervisor to innovate
        $supervisor->update(['company_id' => $innovate->id]);

        // Link supervisor2 to greenleaf
        \App\Models\User::where('email', 'supervisor2@demo.com')
            ->update(['company_id' => $greenleaf->id]);

        $this->command->info('  ✓ Companies seeded (3 companies)');

        return compact('innovate', 'greenleaf');
    }

    // ──────────────────────────────────────────────────────────────────
    // Applications + Hour Logs
    // ──────────────────────────────────────────────────────────────────
    private function seedApplicationsAndHours(array $users, array $companies): void
    {
        $coordinator = $users['coordinator'];
        $supervisor  = $users['supervisor'];
        $students    = $users['students'];

        $scenarios = [
            // Juan — 80% progress, mix of approved + pending logs
            'juan' => [
                'company'   => $companies['innovate'],
                'status'    => 'approved',
                'hours_done'=> 389, // ~80% of 486
                'program'   => 'BS Information Technology',
            ],
            // Maria — 100% done, has evaluation
            'maria' => [
                'company'   => $companies['innovate'],
                'status'    => 'approved',
                'hours_done'=> 486,
                'program'   => 'BS Computer Science',
            ],
            // Pedro — 25% progress, early stage
            'pedro' => [
                'company'   => $companies['greenleaf'],
                'status'    => 'approved',
                'hours_done'=> 122, // ~25%
                'program'   => 'BS Information Technology',
            ],
            // Liza — pending application (no hours yet)
            'liza' => [
                'company'   => $companies['innovate'],
                'status'    => 'pending',
                'hours_done'=> 0,
                'program'   => 'BS Electronics',
            ],
            // Carlo — rejected application
            'carlo' => [
                'company'   => $companies['greenleaf'],
                'status'    => 'rejected',
                'hours_done'=> 0,
                'program'   => 'BS Food Technology',
            ],
        ];

        foreach ($scenarios as $key => $s) {
            $student = $students[$key];

            $app = \App\Models\OjtApplication::updateOrCreate(
                ['student_id' => $student->id, 'company_id' => $s['company']->id],
                [
                    'program'        => $s['program'],
                    'required_hours' => 486,
                    'semester'       => '2nd',
                    'school_year'    => '2024-2025',
                    'status'         => $s['status'],
                    'reviewed_by'    => in_array($s['status'], ['approved', 'rejected']) ? $coordinator->id : null,
                    'reviewed_at'    => in_array($s['status'], ['approved', 'rejected']) ? now()->subDays(60) : null,
                    'remarks'        => $s['status'] === 'rejected'
                        ? 'Incomplete documentary requirements. Please resubmit with complete clearances.'
                        : ($s['status'] === 'approved' ? 'Documents verified. Approved for deployment.' : null),
                    'created_at'     => now()->subDays(65),
                ]
            );

            // Only seed hour logs for approved applications with hours
            if ($s['status'] !== 'approved' || $s['hours_done'] === 0) continue;

            $existingCount = \App\Models\HourLog::where('student_id', $student->id)->count();
            if ($existingCount > 0) continue;

            $this->seedHourLogs($student, $app, $supervisor, $s['hours_done']);
        }

        $this->command->info('  ✓ Applications seeded (5 students, varied stages)');
        $this->command->info('  ✓ Hour logs seeded (Juan ~80%, Maria 100%, Pedro ~25%)');
    }

    private function seedHourLogs(
        \App\Models\User $student,
        \App\Models\OjtApplication $app,
        \App\Models\User $supervisor,
        int $targetHours
    ): void {
        $date       = Carbon::now()->subDays(65);
        $seeded     = 0;
        $dayCount   = 0;

        while ($seeded < $targetHours && $dayCount < 200) {
            $dayCount++;
            if ($date->isWeekend()) {
                $date->addDay();
                continue;
            }

            // Morning session (4h)
            if ($seeded < $targetHours) {
                $hoursThisSession = min(4, $targetHours - $seeded);
                // Last 16 hours are pending (to show pending logs in the UI)
                $isPending = ($targetHours - $seeded) <= 16;

                \App\Models\HourLog::create([
                    'student_id'     => $student->id,
                    'application_id' => $app->id,
                    'date'           => $date->toDateString(),
                    'session'        => 'morning',
                    'time_in'        => '08:00',
                    'time_out'       => '12:00',
                    'total_hours'    => $hoursThisSession,
                    'description'    => $this->logDescription(),
                    'status'         => $isPending ? 'pending' : 'approved',
                    'approved_by'    => $isPending ? null : $supervisor->id,
                    'approved_at'    => $isPending ? null : $date->copy()->setTime(17, 0),
                    'created_at'     => $date->copy()->setTime(12, 0),
                ]);
                $seeded += $hoursThisSession;
            }

            // Afternoon session (4h)
            if ($seeded < $targetHours) {
                $hoursThisSession = min(4, $targetHours - $seeded);
                $isPending = ($targetHours - $seeded) <= 12;

                \App\Models\HourLog::create([
                    'student_id'     => $student->id,
                    'application_id' => $app->id,
                    'date'           => $date->toDateString(),
                    'session'        => 'afternoon',
                    'time_in'        => '13:00',
                    'time_out'       => '17:00',
                    'total_hours'    => $hoursThisSession,
                    'description'    => $this->logDescription(),
                    'status'         => $isPending ? 'pending' : 'approved',
                    'approved_by'    => $isPending ? null : $supervisor->id,
                    'approved_at'    => $isPending ? null : $date->copy()->setTime(17, 30),
                    'created_at'     => $date->copy()->setTime(17, 5),
                ]);
                $seeded += $hoursThisSession;
            }

            $date->addDay();
        }

        // Add one rejected log to show the rejected state and edit flow
        \App\Models\HourLog::create([
            'student_id'       => $student->id,
            'application_id'   => $app->id,
            'date'             => Carbon::now()->subDays(3)->toDateString(),
            'session'          => 'morning',
            'time_in'          => '09:30',
            'time_out'         => '10:00',
            'total_hours'      => 0.5,
            'description'      => 'Arrived late due to flooding.',
            'status'           => 'rejected',
            'rejection_reason' => 'Minimum 4 hours required per session. Please log a full session.',
            'approved_by'      => null,
            'approved_at'      => null,
            'created_at'       => Carbon::now()->subDays(3),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // Weekly Reports
    // ──────────────────────────────────────────────────────────────────
    private function seedWeeklyReports(array $users, array $companies): void
    {
        $coordinator = $users['coordinator'];
        $students    = $users['students'];

        $reportStudents = [
            ['student' => $students['juan'],  'weeks' => 6],
            ['student' => $students['maria'], 'weeks' => 8],
            ['student' => $students['pedro'], 'weeks' => 2],
        ];

        foreach ($reportStudents as $row) {
            $student   = $row['student'];
            $weekCount = $row['weeks'];

            $app = \App\Models\OjtApplication::where('student_id', $student->id)
                ->where('status', 'approved')
                ->first();

            if (! $app) continue;

            $existingCount = \App\Models\WeeklyReport::where('student_id', $student->id)->count();
            if ($existingCount > 0) continue;

            for ($week = 1; $week <= $weekCount; $week++) {
                $weekStart = Carbon::now()->subWeeks($weekCount - $week + 1)->startOfWeek();

                $status = match(true) {
                    $week < ($weekCount - 1) => 'approved',
                    $week === ($weekCount - 1) => 'returned',
                    default => 'pending',
                };

                \App\Models\WeeklyReport::create([
                    'student_id'     => $student->id,
                    'application_id' => $app->id,
                    'week_number'    => $week,
                    'week_start'     => $weekStart->toDateString(),
                    'week_end'       => $weekStart->copy()->addDays(4)->toDateString(),
                    'description'    => $this->reportDescription($week),
                    'status'         => $status,
                    'feedback'       => match($status) {
                        'approved' => 'Well-documented activities. Keep it up!',
                        'returned' => 'Please provide more detail on the technical tasks you completed each day. Add specific tools and technologies used.',
                        default    => null,
                    },
                    'reviewed_by'    => $status !== 'pending' ? $coordinator->id : null,
                    'reviewed_at'    => $status !== 'pending' ? $weekStart->copy()->addDays(7) : null,
                    'created_at'     => $weekStart->copy()->addDays(5),
                ]);
            }
        }

        $this->command->info('  ✓ Weekly reports seeded (Juan 6 weeks, Maria 8 weeks, Pedro 2 weeks)');
    }
    // ──────────────────────────────────────────────────────────────────
    // Evaluations
    // ──────────────────────────────────────────────────────────────────
    private function seedEvaluations(array $users, array $companies): void
    {
        $supervisor = $users['supervisor'];
        $students   = $users['students'];

        // Maria — 100% done, PASS evaluation
        $maria = $students['maria'];
        $mariaApp = \App\Models\OjtApplication::where('student_id', $maria->id)
            ->where('status', 'approved')
            ->first();

        if ($mariaApp && ! \App\Models\Evaluation::where('student_id', $maria->id)->exists()) {
            \App\Models\Evaluation::create([
                'student_id'         => $maria->id,
                'application_id'     => $mariaApp->id,
                'supervisor_id'      => $supervisor->id,
                'attendance_rating'  => 5,
                'performance_rating' => 4,
                'overall_grade'      => 92.5,
                'recommendation'     => 'pass',
                'remarks'            => 'Maria demonstrated exceptional dedication and strong technical skills throughout her internship. She delivered quality work consistently, showed great initiative, and was a pleasure to mentor. Highly recommended for employment.',
                'submitted_at'       => now()->subDays(5),
                'created_at'         => now()->subDays(5),
            ]);
        }

        // Juan — in progress, no evaluation yet (shows the pending state)

        $this->command->info('  ✓ Evaluation seeded (Maria — Pass, 92.5 grade)');
    }

    // ──────────────────────────────────────────────────────────────────
    // QR Clock-In
    // ──────────────────────────────────────────────────────────────────
    private function seedQrClockIn(\App\Models\User $supervisor, \App\Models\Company $company): void
    {
        \App\Models\QrClockIn::firstOrCreate(
            ['company_id' => $company->id],
            [
                'supervisor_id' => $supervisor->id,
                'token'         => \App\Models\QrClockIn::generateToken(),
                'is_active'     => true,
            ]
        );

        $this->command->info('  ✓ QR clock-in token generated for Innovate Tech Solutions');
    }

    // ──────────────────────────────────────────────────────────────────
    // Support Tickets
    // ──────────────────────────────────────────────────────────────────
    private function seedSupportTickets(array $users): void
    {
        $existing = \App\Models\SupportTicket::count();
        if ($existing > 0) {
            $this->command->info('  ✓ Support tickets already exist — skipping');
            return;
        }

        $admin       = $users['admin'];
        $coordinator = $users['coordinator'];
        $supervisor  = $users['supervisor'];
        $juan        = $users['students']['juan'];

        $ticketData = [
            [
                'user'     => $admin,
                'subject'  => 'How do I export student OJT reports to PDF?',
                'type'     => 'general_inquiry',
                'priority' => 'normal',
                'module'   => 'exports',
                'status'   => 'resolved',
                'message'  => "Hi Support,\n\nI'm trying to export the student OJT summary to PDF but can't find the option. I'm on the Premium plan. Can you walk me through it?\n\nThanks,\nDemo Admin",
                'reply'    => "Hi! To export student reports:\n1. Go to **Exports** in the left sidebar\n2. Select \"Student OJT Summary\"\n3. Optionally filter by semester or school year\n4. Click **Download PDF**\n\nLet us know if you need further help!",
            ],
            [
                'user'     => $coordinator,
                'subject'  => 'Hour log total shows incorrect count for Juan dela Cruz',
                'type'     => 'bug',
                'priority' => 'high',
                'module'   => 'hour_logs',
                'status'   => 'in_progress',
                'message'  => "The hours monitoring page shows a different total than the sum of Juan's individual log entries. This might be affecting the progress percentage on his dashboard.\n\nExpected: ~389 hours\nActual displayed: different value\n\nPlease investigate.",
                'reply'    => "Thanks for the detailed report! We were able to reproduce this. The issue appears to be a display rounding difference when summing decimal hours. We're working on a fix and will update this ticket once deployed.",
            ],
            [
                'user'     => $juan,
                'subject'  => 'Cannot see my weekly reports page',
                'type'     => 'bug',
                'priority' => 'high',
                'module'   => 'weekly_reports',
                'status'   => 'resolved',
                'message'  => "When I click on Weekly Reports in the menu, it shows a blank page or redirects me to the dashboard. My application is approved and I should be able to submit reports.\n\nPlease help!",
                'reply'    => "Hi Juan! This is likely because your institution is on the Standard plan which includes weekly reports. The feature should now be visible. Please try logging out and back in. If the issue persists, please let us know!",
            ],
            [
                'user'     => $supervisor,
                'subject'  => 'Feature Request: Bulk approve all pending hour logs',
                'type'     => 'feature_request',
                'priority' => 'normal',
                'module'   => 'hour_logs',
                'status'   => 'open',
                'message'  => "It would be very helpful to have a \"Approve All\" button on the hour logs list page, not just on the individual student view. We have multiple interns and approving logs one-by-one per student is time-consuming.",
                'reply'    => null,
            ],
            [
                'user'     => $admin,
                'subject'  => 'Request to upgrade plan from Standard to Premium',
                'type'     => 'billing',
                'priority' => 'normal',
                'module'   => null,
                'status'   => 'waiting_on_user',
                'message'  => "We'd like to upgrade to the Premium plan to access the analytics dashboard and PDF exports. We currently have 45 active students.\n\nCan you provide the pricing and process for upgrading?\n\nThank you.",
                'reply'    => "Hi! Thank you for your interest in upgrading. I've attached the pricing details for Premium below. You can also submit a formal upgrade request through your admin panel under **Plan & Billing** → **Request Upgrade**.\n\nPremium Plan: ₱30,000/year, unlimited students, all features.\n\nPlease confirm if you'd like to proceed.",
            ],
        ];

        foreach ($ticketData as $data) {
            $ticket = \App\Models\SupportTicket::create([
                'user_id'     => $data['user']->id,
                'subject'     => $data['subject'],
                'type'        => $data['type'],
                'priority'    => $data['priority'],
                'module'      => $data['module'],
                'message'     => $data['message'],
                'status'      => $data['status'],
                'resolved_at' => $data['status'] === 'resolved' ? now()->subDays(1) : null,
                'created_at'  => now()->subDays(rand(2, 14)),
            ]);

            if ($data['reply']) {
                \App\Models\SupportTicketReply::create([
                    'ticket_id'   => $ticket->id,
                    'user_id'     => null,
                    'sender_type' => 'support',
                    'sender_name' => 'OJTConnect Support',
                    'message'     => $data['reply'],
                    'created_at'  => $ticket->created_at->addHours(rand(2, 24)),
                ]);
            }
        }

        $this->command->info('  ✓ Support tickets seeded (5 tickets, varied types and statuses)');
    }

    // ──────────────────────────────────────────────────────────────────
    // Notifications
    // ──────────────────────────────────────────────────────────────────
    private function seedNotifications(array $users): void
    {
        $existing = \App\Models\TenantNotification::count();
        if ($existing > 0) {
            $this->command->info('  ✓ Notifications already exist — skipping');
            return;
        }

        $notifications = [
            // Admin notifications
            [
                'title'       => 'New OJT Application Submitted',
                'message'     => 'Liza Fernandez submitted an OJT application for Innovate Tech Solutions.',
                'type'        => 'info',
                'target_role' => 'admin',
                'user_id'     => $users['admin']->id,
                'is_read'     => false,
            ],
            [
                'title'       => 'Evaluation Submitted',
                'message'     => 'Supervisor Robert Cruz submitted an evaluation for Maria Reyes. Result: Pass. Grade: 92.5',
                'type'        => 'success',
                'target_role' => 'admin',
                'user_id'     => $users['admin']->id,
                'is_read'     => false,
            ],
            [
                'title'       => 'New Support Ticket',
                'message'     => 'Coordinator Ana Santos submitted a bug report: "Hour log total shows incorrect count".',
                'type'        => 'warning',
                'target_role' => 'admin',
                'user_id'     => $users['admin']->id,
                'is_read'     => true,
            ],
            // Coordinator notifications
            [
                'title'       => 'New OJT Application',
                'message'     => 'Liza Fernandez submitted an OJT application for Innovate Tech Solutions. Review required.',
                'type'        => 'info',
                'target_role' => 'ojt_coordinator',
                'user_id'     => $users['coordinator']->id,
                'is_read'     => false,
            ],
            [
                'title'       => 'Weekly Report Submitted',
                'message'     => 'Pedro Garcia submitted his Week 2 report. Pending your review.',
                'type'        => 'info',
                'target_role' => 'ojt_coordinator',
                'user_id'     => $users['coordinator']->id,
                'is_read'     => false,
            ],
            [
                'title'       => 'New Evaluation Submitted',
                'message'     => 'Supervisor submitted evaluation for Maria Reyes. Result: Pass.',
                'type'        => 'success',
                'target_role' => 'ojt_coordinator',
                'user_id'     => $users['coordinator']->id,
                'is_read'     => true,
            ],
            // Supervisor notifications
            [
                'title'       => 'New Hour Log Submitted',
                'message'     => 'Juan dela Cruz submitted 2 hour log(s) for today.',
                'type'        => 'info',
                'target_role' => 'company_supervisor',
                'user_id'     => $users['supervisor']->id,
                'is_read'     => false,
            ],
            [
                'title'       => 'QR Clock-In Recorded',
                'message'     => 'Maria Reyes clocked in (AM) via QR on ' . now()->subDays(1)->format('Y-m-d') . '.',
                'type'        => 'info',
                'target_role' => 'company_supervisor',
                'user_id'     => $users['supervisor']->id,
                'is_read'     => true,
            ],
            // Student notifications
            [
                'title'       => 'Application Approved',
                'message'     => 'Your OJT application for Innovate Tech Solutions has been approved. You may now start logging hours.',
                'type'        => 'success',
                'target_role' => 'student_intern',
                'user_id'     => $users['students']['juan']->id,
                'is_read'     => true,
            ],
            [
                'title'       => 'Hour Log Rejected',
                'message'     => 'Your morning hour log for ' . now()->subDays(3)->format('M d, Y') . ' was rejected. Reason: Minimum 4 hours required per session.',
                'type'        => 'warning',
                'target_role' => 'student_intern',
                'user_id'     => $users['students']['juan']->id,
                'is_read'     => false,
            ],
            [
                'title'       => 'Weekly Report Returned',
                'message'     => 'Your Week ' . 5 . ' report was returned for revision. Feedback: Please provide more detail on technical tasks.',
                'type'        => 'warning',
                'target_role' => 'student_intern',
                'user_id'     => $users['students']['juan']->id,
                'is_read'     => false,
            ],
            [
                'title'       => 'Evaluation Submitted',
                'message'     => 'Your supervisor has submitted your OJT evaluation. Result: Pass. Grade: 92.5.',
                'type'        => 'success',
                'target_role' => 'student_intern',
                'user_id'     => $users['students']['maria']->id,
                'is_read'     => false,
            ],
            [
                'title'       => 'Application Rejected',
                'message'     => 'Your OJT application for GreenLeaf Digital was rejected. Remarks: Incomplete documentary requirements.',
                'type'        => 'warning',
                'target_role' => 'student_intern',
                'user_id'     => $users['students']['carlo']->id,
                'is_read'     => false,
            ],
        ];

        foreach ($notifications as $n) {
            \App\Models\TenantNotification::create($n);
        }

        $this->command->info('  ✓ Notifications seeded (13 notifications across all roles)');
    }

    // ──────────────────────────────────────────────────────────────────
    // TenantUpdate rows — tests the What's New / Install button flow
    // ──────────────────────────────────────────────────────────────────
    private function seedTenantUpdates(Tenant $tenant): void
    {
        $versions = SystemVersion::orderBy('created_at')->get();

        if ($versions->isEmpty()) {
            $this->command->warn('  ⚠ No SystemVersions found — skipping TenantUpdate seeding.');
            $this->command->warn('    Push a release tag to GitHub to create one.');
            return;
        }

        $total = $versions->count();
        $count = 0;

        foreach ($versions as $i => $version) {
            $exists = TenantUpdate::where('tenant_id', $tenant->id)
                ->where('version_id', $version->id)
                ->exists();

            if ($exists) continue;

            // All but the last version = installed, last = pending (to demo install button)
            $isLast  = $i >= ($total - 1);
            $status  = $isLast ? 'pending' : 'completed';

            TenantUpdate::create([
                'tenant_id'    => $tenant->id,
                'version_id'   => $version->id,
                'status'       => $status,
                'installed_at' => $status === 'completed' ? now()->subDays(($total - $i) * 7) : null,
                'installed_by' => $status === 'completed' ? 'admin@demo.com' : null,
            ]);

            $count++;
        }

        $pending   = TenantUpdate::where('tenant_id', $tenant->id)->where('status', 'pending')->count();
        $installed = TenantUpdate::where('tenant_id', $tenant->id)->where('status', 'completed')->count();

        $this->command->info("  ✓ TenantUpdates seeded ({$installed} installed, {$pending} pending)");
    }

    // ──────────────────────────────────────────────────────────────────
    // Content helpers
    // ──────────────────────────────────────────────────────────────────
    private function logDescription(): string
    {
        return \Illuminate\Support\Arr::random([
            'Worked on frontend UI components using Tailwind CSS and Alpine.js.',
            'Fixed authentication bugs in the user login flow.',
            'Attended daily standup and sprint planning with the team.',
            'Integrated REST API endpoints for the inventory module.',
            'Wrote unit tests for the order processing service.',
            'Reviewed and merged pull requests with senior developer.',
            'Created database migration scripts for the new reporting feature.',
            'Debugged mobile responsiveness issues on the landing page.',
            'Deployed updated build to staging environment.',
            'Participated in client requirements gathering meeting.',
            'Documented API endpoints in the Postman collection.',
            'Implemented pagination for the admin dashboard tables.',
            'Set up CI/CD pipeline configuration for the project.',
            'Refactored legacy code to use modern Laravel patterns.',
            'Conducted user acceptance testing with the QA team.',
        ]);
    }

    private function reportDescription(int $week): string
    {
        $descriptions = [
            1 => "During my first week, I was oriented on the company's development workflow, tools, and team structure. I set up my local development environment and was introduced to the main codebase. I attended daily standup meetings and was assigned minor bug fixes to get familiar with the system. I also reviewed the existing documentation and coding standards.",
            2 => "This week I started contributing to the main project. I worked on integrating payment gateway APIs and collaborated with the backend team to consume REST endpoints. I displayed transaction data on the admin dashboard. I also wrote Postman documentation for the three endpoints I worked on. My supervisor reviewed my code and gave helpful feedback on improving error handling.",
            3 => "Week 3 involved building the reporting and analytics feature. I created reusable chart components using Chart.js and connected them to live data endpoints. I attended a sprint retrospective where I presented my progress to the full team. I also helped the QA team reproduce two bugs that were filed against my feature, and fixed both by end of day Friday.",
            4 => "This week focused on testing and quality assurance. I wrote unit tests for all components I built and achieved 85% code coverage on my feature branch. I participated in the user acceptance testing session with the client's representative. I also started working on a new feature request for CSV export functionality.",
            5 => "I completed the CSV export feature and submitted it for code review. My supervisor approved it after two rounds of review. I also helped onboard the new junior developer joining the team, explaining the codebase and workflow. I documented my completed features in the internal wiki.",
            6 => "This week I worked on performance optimization. I identified N+1 query issues in two controllers and fixed them using eager loading, reducing page load times by approximately 40%. I also helped investigate a production bug related to timezone handling and provided the fix.",
            7 => "Penultimate week of my internship. I prepared a technical handover document for all features I worked on. I did a knowledge transfer session with the team and walked through my code contributions. I also worked on final bug fixes raised by the QA team.",
            8 => "Final week of my OJT. I completed all remaining tasks, finalized documentation, and participated in a retrospective with my supervisor. I received my performance evaluation and was commended for my work quality and professionalism. I am grateful for the opportunity and the skills I developed during this internship.",
        ];

        return $descriptions[$week] ?? "Continued assigned tasks and met weekly deliverables.";
    }
}   