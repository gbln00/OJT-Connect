<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates support_tickets and support_ticket_replies tables.
 * Runs inside EACH TENANT's database context.
 * Do NOT use Schema::connection('mysql') here.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── support_tickets ───────────────────────────────────────────
        if (! Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                      ->constrained('users')
                      ->cascadeOnDelete();

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

                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])
                      ->default('normal');

                $table->enum('status', [
                    'open',
                    'in_progress',
                    'waiting_on_user',
                    'resolved',
                    'closed',
                ])->default('open');

                $table->string('module', 100)->nullable();
                $table->text('internal_note')->nullable();
                $table->timestamp('resolved_at')->nullable();

                $table->timestamps();

                // Indexes for the most common query patterns
                $table->index(['user_id', 'status']);
                $table->index(['status', 'priority']);
                $table->index('created_at');
            });
        }

        // ── support_ticket_replies ────────────────────────────────────
        if (! Schema::hasTable('support_ticket_replies')) {
            Schema::create('support_ticket_replies', function (Blueprint $table) {
                $table->id();

                $table->foreignId('ticket_id')
                      ->constrained('support_tickets')
                      ->cascadeOnDelete();

                // Nullable: support team replies have no tenant user_id
                $table->foreignId('user_id')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete();

                // 'user' | 'support'
                $table->enum('sender_type', ['user', 'support'])->default('user');
                $table->string('sender_name', 150)->nullable();

                $table->text('message');
                $table->string('attachment_path')->nullable();
                $table->string('attachment_name')->nullable();

                $table->timestamps();

                $table->index(['ticket_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('support_ticket_replies');
        Schema::dropIfExists('support_tickets');
    }
};
