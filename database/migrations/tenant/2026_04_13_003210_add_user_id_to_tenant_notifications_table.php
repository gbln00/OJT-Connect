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
        Schema::table('tenant_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('tenant_notifications', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('target_role');
                $table->index(['user_id', 'target_role', 'is_read']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_notifications', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'target_role', 'is_read']);
            $table->dropColumn('user_id');
        });
    }
};
