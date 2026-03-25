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
        Schema::create('tenant_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('email')->unique();
            $table->string('subdomain')->unique();      // e.g. "acme" → acme.yourapp.com
            $table->string('contact_person');
            $table->string('phone')->nullable();
            $table->enum('plan', ['basic', 'pro'])->default('basic');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants_registrations');
    }
};
