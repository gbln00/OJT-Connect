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
        // MySQL: modify ENUM to add new value
        DB::statement(
            "ALTER TABLE applications MODIFY COLUMN status
             ENUM('pending','document_review','approved','rejected')
             DEFAULT 'pending'"
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         DB::statement(
            "ALTER TABLE applications MODIFY COLUMN status
             ENUM('pending','approved','rejected')
             DEFAULT 'pending'"
        );
    }
};
