<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mysql')->table('tenants', function (Blueprint $table) {
            // When this tenant's current subscription period ends
            $table->timestamp('plan_expires_at')->nullable()->after('plan');

            // Whether tenant is currently in the 7-day grace period
            $table->boolean('plan_grace')->default(false)->after('plan_expires_at');

            // Timestamp when grace period was entered
            $table->timestamp('grace_started_at')->nullable()->after('plan_grace');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mysql')->table('tenants', function (Blueprint $table) {
            $table->dropColumn(['plan_expires_at', 'plan_grace', 'grace_started_at']);
        });
    }
};
