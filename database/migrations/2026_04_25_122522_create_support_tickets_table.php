<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Runs inside each TENANT's database.
     * Do NOT use Schema::connection('mysql') here.
     */
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();

            // Who submitted it
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Ticket metadata
            $table->string('subject', 255);
            $table->text('message');

            $table->enum('type', [
                'bug',
                'feature_request',
                'general_inquiry',
                'billing',
                'account',
                'other',
            ])->default('general_inquiry');

            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');

            $table->enum('status', [
                'open',
                'in_progress',
                'waiting_on_user',
                'resolved',
                'closed',
            ])->default('open');

            // Optional: which part of the system this relates to
            $table->string('module', 100)->nullable(); // e.g. 'hour_logs', 'applications'

            // Super-admin internal note (not visible to tenant user)
            $table->text('internal_note')->nullable();

            // When the ticket was resolved / closed
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index(['user_id', 'status']);
            $table->index(['status', 'priority']);
        });

        Schema::create('support_ticket_replies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ticket_id')
                  ->constrained('support_tickets')
                  ->onDelete('cascade');

            // Can be a tenant user OR null when reply comes from super-admin
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Identifies who wrote the reply so we can style it differently
            $table->enum('sender_type', ['user', 'support'])->default('user');
            $table->string('sender_name', 255)->nullable(); // snapshot of name at reply time

            $table->text('message');

            // File attachment (stored in tenant storage)
            $table->string('attachment_path', 500)->nullable();
            $table->string('attachment_name', 255)->nullable();

            $table->timestamps();

            $table->index('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_replies');
        Schema::dropIfExists('support_tickets');
    }
};