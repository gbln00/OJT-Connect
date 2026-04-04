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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();          // basic | standard | premium
            $table->string('label');                   // Display name, e.g. "Basic"
            $table->unsignedInteger('base_price');     // in PHP pesos, e.g. 10000
            $table->string('billing_cycle')->default('yearly');
            $table->unsignedInteger('student_cap')->nullable(); // null = unlimited
            $table->json('features');                  // {"weekly_reports":true,"pdf_export":false,...}
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
