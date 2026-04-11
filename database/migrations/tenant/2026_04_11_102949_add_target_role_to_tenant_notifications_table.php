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
            if (!Schema::hasColumn('tenant_notifications', 'target_role')) {
                $table->string('target_role')->default('admin')->after('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_notifications', function (Blueprint $table) {
            $table->dropColumn('target_role');
        });
    }
};
