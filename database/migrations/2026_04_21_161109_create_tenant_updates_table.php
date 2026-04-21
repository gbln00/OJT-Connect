<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql')->create('tenant_updates', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('version_id')
                  ->constrained('system_versions')
                  ->onDelete('cascade');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])
                  ->default('pending');
            $table->timestamp('installed_at')->nullable();
            $table->string('installed_by')->nullable(); // email of user who triggered
            $table->text('error_log')->nullable();       // store exception on failure
            $table->integer('migration_batch')->nullable(); // DB batch number after migrate
            $table->timestamps();

            $table->unique(['tenant_id', 'version_id']); // one record per tenant per version
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::connection('mysql')->dropIfExists('tenant_updates');
    }
};