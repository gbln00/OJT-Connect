<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
        /**
        * Run the database seeds.
        */
    public function run(): void
    {
        // Wipe existing plans cleanly (avoids duplicate key errors on re-run)
        DB::connection('mysql')->table('plan_promotions')->delete();
        DB::connection('mysql')->table('plans')->delete();

        $plans = [

            // ── 1. BASIC ────────────────────────────────────────────────
            [
                'name'          => 'basic',
                'label'         => 'Basic',
                'description'   => 'Essential OJT management for small cohorts.',
                'base_price'    => 10000,
                'billing_cycle' => 'yearly',
                'student_cap'   => 50,
                'sort_order'    => 1,
                'is_active'     => true,
                'features'      => [
                    // ✓ Included
                    'hour_logs'      => true,   // OJT hour monitoring
                    'email_notifs'   => true,   // Basic notifications

                    // ✗ Not included on Basic
                    'weekly_reports' => false,  // Online report submission (Standard+)
                    'evaluations'    => false,  // Student evaluation system (Standard+)
                    'pdf_export'     => false,  // Automated PDF generation (Premium only)
                    'excel_export'   => false,  // Advanced analytics (Premium only)
                ],
            ],

            // ── 2. STANDARD ─────────────────────────────────────────────
            [
                'name'          => 'standard',
                'label'         => 'Standard',
                'description'   => 'Full monitoring with evaluations and report submission.',
                'base_price'    => 20000,
                'billing_cycle' => 'yearly',
                'student_cap'   => 150,
                'sort_order'    => 2,
                'is_active'     => true,
                'features'      => [
                    // ✓ Everything in Basic
                    'hour_logs'      => true,
                    'email_notifs'   => true,

                    // ✓ Standard additions
                    'weekly_reports' => true,   // Online report submission
                    'evaluations'    => true,   // Student evaluation system

                    // ✗ Premium only
                    'pdf_export'     => false,
                    'excel_export'   => false,
                ],
            ],

            // ── 3. PREMIUM ──────────────────────────────────────────────
            [
                'name'          => 'premium',
                'label'         => 'Premium',
                'description'   => 'Unlimited students, advanced analytics, and automated PDF reports.',
                'base_price'    => 30000,
                'billing_cycle' => 'yearly',
                'student_cap'   => null,        // Unlimited student records
                'sort_order'    => 3,
                'is_active'     => true,
                'features'      => [
                    // ✓ Everything in Standard
                    'hour_logs'      => true,
                    'email_notifs'   => true,
                    'weekly_reports' => true,
                    'evaluations'    => true,

                    // ✓ Premium additions
                    'pdf_export'     => true,   // Automated PDF generation
                    'excel_export'   => true,   // Advanced reports & analytics
                ],
            ],

        ];

        foreach ($plans as $data) {
            Plan::create($data);
        }

        $this->command->info('✓ Plans seeded:');
        $this->command->table(
            ['Name', 'Label', 'Price', 'Student Cap', 'Features'],
            collect($plans)->map(fn($p) => [
                $p['name'],
                $p['label'],
                '₱' . number_format($p['base_price']),
                $p['student_cap'] ? $p['student_cap'] : 'Unlimited',
                collect($p['features'])->filter()->keys()->implode(', '),
            ])->toArray()
        );
    }
}