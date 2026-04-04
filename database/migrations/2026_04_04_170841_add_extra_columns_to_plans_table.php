<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds new columns to the central `plans` table.
     */
    public function up(): void
    {
        Schema::connection('mysql')->table('plans', function (Blueprint $table) {
            // Billing cycle: monthly or yearly
            if (!Schema::connection('mysql')->hasColumn('plans', 'billing_cycle')) {
                $table->string('billing_cycle', 20)->default('yearly')->after('base_price');
            }
            // Optional plan-wide renewal/expiry date
            if (!Schema::connection('mysql')->hasColumn('plans', 'renewal_date')) {
                $table->date('renewal_date')->nullable()->after('billing_cycle');
            }
            // Short description shown to tenants
            if (!Schema::connection('mysql')->hasColumn('plans', 'description')) {
                $table->string('description', 500)->nullable()->after('renewal_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('plans', function (Blueprint $table) {
            $table->dropColumn(['billing_cycle', 'renewal_date', 'description']);
        });
    }
};