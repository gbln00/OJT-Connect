<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // database/seeders/PlanSeeder.php
        Plan::insert([
            ['name'=>'basic',    'label'=>'Basic',    'base_price'=>10000, 'student_cap'=>50,  'sort_order'=>1,
            'features'=>json_encode(['weekly_reports'=>false,'evaluations'=>false,'pdf_export'=>false,'excel_export'=>false,'advanced_analytics'=>false,'certificate'=>false])],
            ['name'=>'standard', 'label'=>'Standard', 'base_price'=>20000, 'student_cap'=>200, 'sort_order'=>2,
            'features'=>json_encode(['weekly_reports'=>true,'evaluations'=>true,'pdf_export'=>false,'excel_export'=>false,'advanced_analytics'=>false,'certificate'=>false])],
            ['name'=>'premium',  'label'=>'Premium',  'base_price'=>30000, 'student_cap'=>null,'sort_order'=>3,
            'features'=>json_encode(['weekly_reports'=>true,'evaluations'=>true,'pdf_export'=>true,'excel_export'=>true,'advanced_analytics'=>true,'certificate'=>true])],
        ]);
    }
}
