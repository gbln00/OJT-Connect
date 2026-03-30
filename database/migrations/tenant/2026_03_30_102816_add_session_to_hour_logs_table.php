<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hour_logs', function (Blueprint $table) {
            // 'morning' or 'afternoon'
            $table->enum('session', ['morning', 'afternoon'])
                  ->default('morning')
                  ->after('date');
        });
    }

    public function down(): void
    {
        Schema::table('hour_logs', function (Blueprint $table) {
            $table->dropColumn('session');
        });
    }
};