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
        Schema::connection('mysql')->create('tenant_bandwidth_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->date('date')->index();
            $table->unsignedBigInteger('bytes_in')->default(0);   // request body size
            $table->unsignedBigInteger('bytes_out')->default(0);  // response size (already tracked)
            $table->unsignedInteger('request_count')->default(0); // requests that day
            $table->timestamps();

            $table->unique(['tenant_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
   public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('tenant_bandwidth_logs');
    }
};
