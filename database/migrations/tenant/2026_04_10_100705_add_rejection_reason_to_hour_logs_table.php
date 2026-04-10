<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hour_logs', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('status');
        });
        
        DB::statement("ALTER TABLE hour_logs MODIFY status ENUM('pending','approved','rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hour_logs', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });

         DB::statement("ALTER TABLE hour_logs MODIFY status ENUM('pending','approved') DEFAULT 'pending'");
    }
};
