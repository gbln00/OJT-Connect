<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql')->create('system_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20);                      // e.g. '1.4.0'
            $table->string('label')->nullable();                 // e.g. 'Hotfix Release'
            $table->enum('type', ['major','minor','patch','hotfix'])->default('minor');
            $table->text('changelog');                          // Markdown content
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); // super_admin user ID
            $table->timestamps();
        });

        // Per-tenant read receipts
        Schema::connection('mysql')->create('version_read_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('version_id')->constrained('system_versions')->onDelete('cascade');
            $table->string('tenant_id');
            $table->string('read_by');      // admin user email
            $table->timestamp('read_at');
            $table->timestamps();
            $table->unique(['version_id', 'tenant_id', 'read_by']);
        });
    }

    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('version_read_receipts');
        Schema::connection('mysql')->dropIfExists('system_versions');
    }
};