<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');

            // Application details
            $table->string('program');           // e.g. BSIT, BSBA
            $table->string('school_year');        // e.g. 2024-2025
            $table->string('semester');           // e.g. 1st, 2nd, Summer
            $table->unsignedInteger('required_hours')->default(600);

            // Document upload
            $table->string('document_path')->nullable(); // stored file path

            // Status & review
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('remarks')->nullable();         // admin/coordinator notes
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};