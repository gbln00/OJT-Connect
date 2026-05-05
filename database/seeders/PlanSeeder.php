<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mysql')->table('tenant_plan_histories')->delete(); 
        DB::connection('mysql')->table('plan_promotions')->delete();
        DB::connection('mysql')->table('plans')->delete();

        $plans = [

            // ── BASIC ────────────────────────────────────────────────
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
                    'hour_logs'            => true,
                    'qr_clock_in'          => true,
                    'email_notifs'         => true,
                    'support_tickets'      => true,   // ✓ All plans
                    'two_factor_auth'      => true,   // ✓ All plans

                    'weekly_reports'       => false,  // Standard+
                    'evaluations'          => false,  // Standard+
                    'csv_import'           => false,  // Standard+

                    'pdf_export'           => false,  // Premium only
                    'excel_export'         => false,  // Premium only
                    'analytics_dashboard'  => false,  // Premium only
                    'tenant_customization' => false,  // Premium only
                ],
            ],

            // ── STANDARD ─────────────────────────────────────────────
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
                    'hour_logs'            => true,
                    'qr_clock_in'          => true,
                    'email_notifs'         => true,
                    'support_tickets'      => true,
                    'two_factor_auth'      => true,

                    'weekly_reports'       => true,   // ✓ Standard+
                    'evaluations'          => true,   // ✓ Standard+
                    'csv_import'           => true,   // ✓ Standard+

                    'pdf_export'           => false,  // Premium only
                    'excel_export'         => false,  // Premium only
                    'analytics_dashboard'  => false,  // Premium only
                    'tenant_customization' => false,  // Premium only
                ],
            ],

            // ── PREMIUM ──────────────────────────────────────────────
            [
                'name'          => 'premium',
                'label'         => 'Premium',
                'description'   => 'Unlimited students, advanced analytics, and automated PDF reports.',
                'base_price'    => 30000,
                'billing_cycle' => 'yearly',
                'student_cap'   => null,
                'sort_order'    => 3,
                'is_active'     => true,
                'features'      => [
                    'hour_logs'            => true,
                    'qr_clock_in'          => true,
                    'email_notifs'         => true,
                    'support_tickets'      => true,
                    'two_factor_auth'      => true,

                    'weekly_reports'       => true,
                    'evaluations'          => true,
                    'csv_import'           => true,

                    'pdf_export'           => true,   // ✓ Premium
                    'excel_export'         => true,   // ✓ Premium
                    'analytics_dashboard'  => true,   // ✓ Premium
                    'tenant_customization' => true,   // ✓ Premium
                ],
            ],
        ];

        foreach ($plans as $data) {
            Plan::create($data);
        }

        $this->command->info('✓ Plans seeded successfully.');
    }
}