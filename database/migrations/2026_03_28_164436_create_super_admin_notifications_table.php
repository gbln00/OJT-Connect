<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('super_admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');        // e.g. 'registration', 'approval', 'tenant', 'status'
            $table->string('title');
            $table->string('message');
            $table->string('icon')->default('bell');   // bell | check | x | plus | toggle
            $table->string('link')->nullable();         // optional route to link to
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('super_admin_notifications');
    }
};