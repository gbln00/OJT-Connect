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
        Schema::create('tenant_request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('method', 10);
            $table->string('path', 500);
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('response_size')->nullable(); // bytes
            $table->string('ip', 45)->nullable();
            $table->timestamp('logged_at')->useCurrent()->index();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('tenant_request_logs');
    }

};
