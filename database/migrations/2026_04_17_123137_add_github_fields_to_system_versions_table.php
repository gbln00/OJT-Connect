<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mysql')->table('system_versions', function (Blueprint $table) {
            // GitHub integration fields
            $table->string('github_release_id')->nullable()->after('version');
            $table->string('github_tag')->nullable()->after('github_release_id');
            $table->string('github_html_url')->nullable()->after('github_tag');

            // Update control fields
            $table->string('migration_folder')->nullable()->after('changelog');
            // e.g. 'versions/v1-3-0' — relative to database/migrations/
            $table->string('requires_version')->nullable()->after('migration_folder');
            // Tenants must install this version before this one
            $table->boolean('is_critical')->default(false)->after('requires_version');
            // If true, tenants are blocked until they install this
        });
    }

    public function down(): void
    {
        Schema::connection('mysql')->table('system_versions', function (Blueprint $table) {
            $table->dropColumn([
                'github_release_id', 'github_tag', 'github_html_url',
                'migration_folder', 'requires_version', 'is_critical',
            ]);
        });
    }
};