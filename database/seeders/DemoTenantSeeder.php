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
 * DemoTenantSeeder
 *
 * Creates a fully populated demo tenant at demo.ojt-connect.xyz
 * Safe to run multiple times — skips creation if tenant already exists.
 *
 * Usage:
 *   php artisan db:seed --class=DemoTenantSeeder
 *
 * Credentials after seeding:
 *   URL:         https://demo.ojt-connect.xyz
 *   Admin:       admin@demo.com       / Demo@Admin123
 *   Coordinator: coordinator@demo.com / Demo@123
 *   Supervisor:  supervisor@demo.com  / Demo@123
 *   Student 1:   juan@demo.com        / Demo@123
 *   Student 2:   maria@demo.com       / Demo@123
 *   Student 3:   pedro@demo.com       / Demo@123
 */
class DemoTenantSeeder extends Seeder
{
    // ── Tenant config ─────────────────────────────────────────────────
    const TENANT_ID     = 'demo';
    const TENANT_DOMAIN = 'demo.ojt-connect.xyz';
    const TENANT_NAME   = 'Demo University — College of Technology';
    const TENANT_PLAN   = 'premium'; // premium = all features unlocked for demo

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('── DemoTenantSeeder ─────────────────────────────────');

        // ── 1. Create or fetch the tenant ──────────────────────────────
        $tenant = Tenant::find(self::TENANT_ID);

        if ($tenant) {
            $this->command->warn('  Demo tenant already exists — refreshing data only.');
        } else {
            $tenant = Tenant::create([
                'id'               => self::TENANT_ID,
                'name'             => self::TENANT_NAME,
                'plan'             => self::TENANT_PLAN,
                'status'           => 'active',
                'plan_expires_at'  => now()->addYear(),
            ]);

            $tenant->domains()->create([
                'domain' => self::TENANT_DOMAIN,
            ]);

            $this->command->info('  ✓ Tenant created: ' . self::TENANT_DOMAIN);
        }

        // ── 2. Seed all tenant-context data ────────────────────────────
        $tenant->run(function () {
            $this->seedUsers();
            $this->seedCompanies();
            $this->seedApplicationsAndHours();
            $this->seedWeeklyReports();
            $this->seedEvaluations();
            $this->seedSupportTickets();
        });

        // ── 3. Seed TenantUpdate rows (for testing the Updates feature) ─
        $this->seedTenantUpdates($tenant);

        $this->command->info('');
        $this->command->info('  ✓ Demo tenant fully seeded!');
        $this->command->info('  URL:         https://' . self::TENANT_DOMAIN);
        $this->command->info('  Admin:       admin@demo.com / Demo@Admin123');
        $this->command->info('  Coordinator: coordinator@demo.com / Demo@123');
        $this->command->info('  Supervisor:  supervisor@demo.com / Demo@123');
        $this->command->info('  Students:    juan@demo.com, maria@demo.com, pedro@demo.com / Demo@123');
        $this->command->info('────────────────────────────────────────────────────');
        $this->command->info('');
    }

    // ──────────────────────────────────────────────────────────────────
    // Users
    // ──────────────────────────────────────────────────────────────────
    private function seedUsers(): void
    {
        $users = [
            [
                'name'        => 'Demo Admin',
                'email'       => 'admin@demo.com',
                'password'    => Hash::make('Demo@Admin123'),
                'role'        => 'admin',
                'is_active'   => true,
            ],
            [
                'name'        => 'Ana Santos',
                'email'       => 'coordinator@demo.com',
                'password'    => Hash::make('Demo@123'),
                'role'        => 'ojt_coordinator',
                'is_active'   => true,
            ],
            [
                'name'        => 'Robert Cruz',
                'email'       => 'supervisor@demo.com',
                'password'    => Hash::make('Demo@123'),
                'role'        => 'company_supervisor',
                'is_active'   => true,
            ],
            [
                'name'        => 'Juan dela Cruz',
                'email'       => 'juan@demo.com',
                'password'    => Hash::make('Demo@123'),
                'role'        => 'student_intern',
                'is_active'   => true,
                'student_id'  => '2021-00101',
                'course'      => 'BSIT',
                'year_level'  => '4th Year',
                'section'     => '4A',
            ],
            [
                'name'        => 'Maria Reyes',
                'email'       => 'maria@demo.com',
                'password'    => Hash::make('Demo@123'),
                'role'        => 'student_intern',
                'is_active'   => true,
                'student_id'  => '2021-00102',
                'course'      => 'BSCS',
                'year_level'  => '4th Year',
                'section'     => '4B',
            ],
            [
                'name'        => 'Pedro Garcia',
                'email'       => 'pedro@demo.com',
                'password'    => Hash::make('Demo@123'),
                'role'        => 'student_intern',
                'is_active'   => true,
                'student_id'  => '2021-00103',
                'course'      => 'BSIT',
                'year_level'  => '4th Year',
                'section'     => '4A',
            ],
        ];

        foreach ($users as $data) {
            \App\Models\User::firstOrCreate(
                ['email' => $data['email']],
                $data
            );
        }

        $this->command->info('  ✓ Users seeded (6 users)');
    }

    // ──────────────────────────────────────────────────────────────────
    // Companies
    // ──────────────────────────────────────────────────────────────────
    private function seedCompanies(): void
    {
        $companies = [
            [
                'name'           => 'Innovate Tech Solutions',
                'industry'       => 'Information Technology',
                'address'        => 'Cagayan de Oro City, Misamis Oriental',
                'contact_person' => 'Robert Cruz',
                'contact_email'  => 'hr@innovatetech.com',
                'contact_phone'  => '09171234567',
                'is_active'      => true,
            ],
            [
                'name'           => 'GreenLeaf Digital',
                'industry'       => 'Digital Marketing',
                'address'        => 'Iligan City, Lanao del Norte',
                'contact_person' => 'Sofia Mendez',
                'contact_email'  => 'careers@greenleaf.digital',
                'contact_phone'  => '09281234567',
                'is_active'      => true,
            ],
        ];

        foreach ($companies as $data) {
            \App\Models\Company::firstOrCreate(
                ['name' => $data['name']],
                $data
            );
        }

        $this->command->info('  ✓ Companies seeded (2 companies)');
    }

    // ──────────────────────────────────────────────────────────────────
    // Applications + Hour Logs
    // ──────────────────────────────────────────────────────────────────
    private function seedApplicationsAndHours(): void
    {
        $company1 = \App\Models\Company::where('name', 'Innovate Tech Solutions')->first();
        $company2 = \App\Models\Company::where('name', 'GreenLeaf Digital')->first();

        $students = [
            ['email' => 'juan@demo.com',  'company' => $company1, 'hours' => 320, 'required' => 486],
            ['email' => 'maria@demo.com', 'company' => $company1, 'hours' => 486, 'required' => 486],
            ['email' => 'pedro@demo.com', 'company' => $company2, 'hours' => 120, 'required' => 486],
        ];

        $supervisor = \App\Models\User::where('email', 'supervisor@demo.com')->first();
        $coordinator = \App\Models\User::where('email', 'coordinator@demo.com')->first();

        foreach ($students as $s) {
            $student = \App\Models\User::where('email', $s['email'])->first();
            if (! $student) continue;

            // Application
            $app = \App\Models\Application::firstOrCreate(
                ['student_id' => $student->id, 'company_id' => $s['company']->id],
                [
                    'student_id'     => $student->id,
                    'company_id'     => $s['company']->id,
                    'program'        => $student->course ?? 'BSIT',
                    'required_hours' => $s['required'],
                    'semester'       => '2nd',
                    'school_year'    => '2024-2025',
                    'status'         => 'approved',
                    'document_path'  => null,
                    'reviewed_by'    => $coordinator?->id,
                    'reviewed_at'    => now()->subDays(60),
                    'created_at'     => now()->subDays(65),
                ]
            );

            // Hour logs — spread over past weeks
            $existingLogs = \App\Models\HourLog::where('student_id', $student->id)->count();
            if ($existingLogs === 0) {
                $hoursToSeed = $s['hours'];
                $date = now()->subDays(60);
                $hoursSeeded = 0;

                while ($hoursSeeded < $hoursToSeed) {
                    // Skip weekends
                    if ($date->isWeekend()) {
                        $date->addDay();
                        continue;
                    }

                    $daily = min(8, $hoursToSeed - $hoursSeeded);

                    \App\Models\HourLog::create([
                        'student_id'     => $student->id,
                        'application_id' => $app->id,
                        'date'           => $date->toDateString(),
                        'time_in'        => '08:00:00',
                        'time_out'       => Carbon::parse('08:00:00')->addHours($daily)->format('H:i:s'),
                        'total_hours'    => $daily,
                        'description'    => $this->randomLogDescription(),
                        'status'         => $hoursSeeded < ($hoursToSeed - 16) ? 'approved' : 'pending',
                        'approved_by'    => $supervisor?->id,
                        'created_at'     => $date->copy(),
                    ]);

                    $hoursSeeded += $daily;
                    $date->addDay();
                }
            }
        }

        $this->command->info('  ✓ Applications + hour logs seeded');
    }

    // ──────────────────────────────────────────────────────────────────
    // Weekly Reports
    // ──────────────────────────────────────────────────────────────────
    private function seedWeeklyReports(): void
    {
        $students = \App\Models\User::where('role', 'student_intern')->get();
        $coordinator = \App\Models\User::where('email', 'coordinator@demo.com')->first();

        foreach ($students as $student) {
            $app = \App\Models\Application::where('student_id', $student->id)->first();
            if (! $app) continue;

            $existingReports = \App\Models\WeeklyReport::where('student_id', $student->id)->count();
            if ($existingReports > 0) continue;

            // Seed 4 weekly reports per student
            for ($week = 1; $week <= 4; $week++) {
                $status = match(true) {
                    $week <= 2  => 'approved',
                    $week === 3 => 'returned',
                    default     => 'pending',
                };

                \App\Models\WeeklyReport::create([
                    'student_id'     => $student->id,
                    'application_id' => $app->id,
                    'week_number'    => $week,
                    'week_start'     => now()->subWeeks(5 - $week)->startOfWeek()->toDateString(),
                    'week_end'       => now()->subWeeks(5 - $week)->endOfWeek()->toDateString(),
                    'description'    => $this->randomReportDescription($week),
                    'status'         => $status,
                    'feedback'       => $status === 'returned'
                        ? 'Please add more detail about the tasks you completed each day.'
                        : ($status === 'approved' ? 'Good report. Keep it up!' : null),
                    'reviewed_by'    => in_array($status, ['approved', 'returned']) ? $coordinator?->id : null,
                    'reviewed_at'    => in_array($status, ['approved', 'returned']) ? now()->subDays(3) : null,
                    'created_at'     => now()->subWeeks(5 - $week),
                ]);
            }
        }

        $this->command->info('  ✓ Weekly reports seeded');
    }

    // ──────────────────────────────────────────────────────────────────
    // Evaluations
    // ──────────────────────────────────────────────────────────────────
    private function seedEvaluations(): void
    {
        $supervisor = \App\Models\User::where('email', 'supervisor@demo.com')->first();

        // Only seed evaluation for student who completed hours (maria)
        $maria = \App\Models\User::where('email', 'maria@demo.com')->first();
        if (! $maria || ! $supervisor) return;

        $app = \App\Models\Application::where('student_id', $maria->id)->first();
        if (! $app) return;

        $exists = \App\Models\Evaluation::where('student_id', $maria->id)->exists();
        if ($exists) return;

        \App\Models\Evaluation::create([
            'student_id'         => $maria->id,
            'supervisor_id'      => $supervisor->id,
            'application_id'     => $app->id,
            'attendance_rating'  => 5,
            'performance_rating' => 4,
            'overall_grade'      => 90.0,
            'recommendation'     => 'pass',
            'remarks'            => 'Maria demonstrated exceptional dedication and technical skills throughout her internship. She consistently delivered quality work and showed great initiative.',
            'submitted_at'       => now()->subDays(5),
            'created_at'         => now()->subDays(5),
        ]);

        $this->command->info('  ✓ Evaluation seeded (Maria — Pass, 90.0)');
    }

    // ──────────────────────────────────────────────────────────────────
    // Support Tickets
    // ──────────────────────────────────────────────────────────────────
    private function seedSupportTickets(): void
    {
        $admin = \App\Models\User::where('email', 'admin@demo.com')->first();
        $coordinator = \App\Models\User::where('email', 'coordinator@demo.com')->first();
        $student = \App\Models\User::where('email', 'juan@demo.com')->first();

        if (! $admin) return;

        $existing = \App\Models\SupportTicket::count();
        if ($existing > 0) return;

        $tickets = [
            [
                'user_id'  => $admin->id,
                'subject'  => 'How do I export student reports to PDF?',
                'type'     => 'general_inquiry',
                'priority' => 'normal',
                'module'   => 'exports',
                'message'  => "Hi, I'm trying to export the student OJT summary to PDF but I can't find the export button. I'm on the Premium plan so it should be available. Can you guide me?\n\nThanks,\nDemo Admin",
                'status'   => 'resolved',
                'resolved_at' => now()->subDays(2),
            ],
            [
                'user_id'  => $coordinator?->id ?? $admin->id,
                'subject'  => 'Hour log showing incorrect total hours',
                'type'     => 'bug',
                'priority' => 'high',
                'module'   => 'hour_logs',
                'message'  => "When I view Juan dela Cruz's hour log summary, the total shows 312 hours but should be 320 based on the individual log entries. This may be affecting the progress percentage displayed on his dashboard.\n\nSteps to reproduce:\n1. Go to Hours Monitoring\n2. Click View Logs for Juan dela Cruz\n3. Compare individual log totals vs the displayed summary\n\nExpected: 320 hours\nActual: shows 312",
                'status'   => 'in_progress',
            ],
            [
                'user_id'  => $student?->id ?? $admin->id,
                'subject'  => 'Request: Add ability to attach multiple files to weekly report',
                'type'     => 'feature_request',
                'priority' => 'low',
                'module'   => 'weekly_reports',
                'message'  => "Currently the weekly report form only allows one file attachment. It would be very helpful if we could attach multiple files (e.g., screenshots, documentation, certificates) per report submission.\n\nThis would help students provide better evidence of their work each week.",
                'status'   => 'open',
            ],
        ];

        foreach ($tickets as $data) {
            $ticket = \App\Models\SupportTicket::create(array_merge($data, [
                'ref'        => \App\Models\SupportTicket::generateRef(),
                'created_at' => now()->subDays(rand(3, 10)),
            ]));

            // Add a support reply to the resolved ticket
            if ($data['status'] === 'resolved') {
                \App\Models\SupportTicketReply::create([
                    'ticket_id'   => $ticket->id,
                    'user_id'     => null,
                    'sender_type' => 'support',
                    'sender_name' => 'Support Team',
                    'display_name'=> 'OJTConnect Support',
                    'message'     => "Hi! Thanks for reaching out.\n\nTo export student reports to PDF:\n1. Go to **Exports** in the left sidebar (under the Premium section)\n2. You'll see three export cards — select \"Student OJT Summary\"\n3. Optionally filter by semester/school year\n4. Click \"Download PDF\"\n\nLet us know if you need any further help!",
                    'created_at'  => now()->subDays(1),
                ]);
            }

            // Add a support reply to the in-progress ticket
            if ($data['status'] === 'in_progress') {
                \App\Models\SupportTicketReply::create([
                    'ticket_id'   => $ticket->id,
                    'user_id'     => null,
                    'sender_type' => 'support',
                    'sender_name' => 'Support Team',
                    'display_name'=> 'OJTConnect Support',
                    'message'     => "Thanks for the detailed report! We were able to reproduce this issue. It appears to be a rounding difference between how `total_hours` is stored vs summed. We're looking into it and will push a fix shortly.\n\nWe'll update this ticket once it's deployed.",
                    'created_at'  => now()->subDays(1),
                ]);
            }
        }

        $this->command->info('  ✓ Support tickets seeded (3 tickets with replies)');
    }

    // ──────────────────────────────────────────────────────────────────
    // TenantUpdate rows — lets you test the "Install" button
    // ──────────────────────────────────────────────────────────────────
    private function seedTenantUpdates(Tenant $tenant): void
    {
        $versions = SystemVersion::orderBy('created_at')->get();

        if ($versions->isEmpty()) {
            $this->command->warn('  ⚠ No SystemVersions found — skipping TenantUpdate seeding.');
            $this->command->warn('    Push a version tag to GitHub to create one, then re-run.');
            return;
        }

        $count = 0;
        foreach ($versions as $i => $version) {
            $exists = TenantUpdate::where('tenant_id', $tenant->id)
                ->where('version_id', $version->id)
                ->exists();

            if ($exists) continue;

            // Older versions = installed, latest 1-2 = pending (to demo the install button)
            $isLast  = $i >= ($versions->count() - 2);
            $status  = $isLast ? 'pending' : 'completed';

            TenantUpdate::create([
                'tenant_id'    => $tenant->id,
                'version_id'   => $version->id,
                'status'       => $status,
                'installed_at' => $status === 'completed' ? now()->subDays(($versions->count() - $i) * 7) : null,
                'installed_by' => $status === 'completed' ? 'admin@demo.com' : null,
                'notified_at'  => now()->subDays(1),
            ]);

            $count++;
        }

        $pending  = TenantUpdate::where('tenant_id', $tenant->id)->where('status', 'pending')->count();
        $installed = TenantUpdate::where('tenant_id', $tenant->id)->where('status', 'completed')->count();

        $this->command->info("  ✓ TenantUpdates seeded ({$installed} installed, {$pending} pending — ready to test Install button)");
    }

    // ──────────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────────
    private function randomLogDescription(): string
    {
        $descriptions = [
            'Worked on frontend UI components using Tailwind CSS.',
            'Fixed bugs in the user authentication module.',
            'Attended morning standup and sprint planning session.',
            'Integrated REST API endpoints for the inventory module.',
            'Wrote unit tests for the order processing service.',
            'Reviewed pull requests and merged approved code.',
            'Set up local development environment for new project.',
            'Created database migration scripts for feature branch.',
            'Collaborated with senior dev on code review session.',
            'Documented API endpoints in Postman collection.',
            'Debugged CSS layout issues on mobile responsiveness.',
            'Deployed updated build to staging server.',
        ];

        return $descriptions[array_rand($descriptions)];
    }

    private function randomReportDescription(int $week): string
    {
        $reports = [
            1 => "During my first week, I was oriented on the company's development workflow and tools. I set up my local environment and was introduced to the codebase. I attended daily standups and helped fix minor UI bugs assigned to me by my supervisor.",
            2 => "This week I worked on integrating the payment gateway module. I collaborated with the backend team to consume REST APIs and display transaction data on the dashboard. I also wrote documentation for the endpoints I worked on.",
            3 => "Week 3 involved working on the reporting feature. I built several chart components and connected them to live data endpoints. I also attended a sprint retrospective where I presented my progress to the team.",
            4 => "This week I focused on testing and quality assurance. I wrote unit tests for the components I built and helped QA test the full user flow. I also started working on a new feature request from the product team.",
        ];

        return $reports[$week] ?? "Continued work on assigned tasks and met all weekly goals.";
    }
}