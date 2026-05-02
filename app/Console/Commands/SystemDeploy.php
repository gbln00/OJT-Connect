<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SystemDeploy extends Command
{
    protected $signature   = 'system:deploy {--skip-composer : Skip composer install}';
    protected $description = 'Pull latest code from GitHub and rebuild the application';

    public function handle(): int
    {
        $projectDir = base_path();

        $this->info('🚀 Starting server-level deployment...');

        try {
            // 1. Put app in maintenance mode
            $this->call('down', ['--render' => 'errors::503']);

            // 2. Pull latest code
            $this->info('[1/5] Pulling code from GitHub...');
            $this->exec("cd {$projectDir} && git fetch origin && git reset --hard origin/main");

            // 3. Composer install
            if (! $this->option('skip-composer')) {
                $this->info('[2/5] Installing dependencies...');
                $this->exec("cd {$projectDir} && composer install --no-dev --optimize-autoloader --no-interaction");
            }

            // 4. Central DB migrations (system_versions, tenant_updates, etc.)
            $this->info('[3/5] Running central migrations...');
            $this->call('migrate', ['--force' => true]);

            // 5. Clear and rebuild caches
            $this->info('[4/5] Rebuilding caches...');
            $this->call('optimize:clear');
            $this->call('config:cache');
            $this->call('route:cache');
            $this->call('view:cache');

            // 6. Restart queue workers
            $this->info('[5/5] Restarting queue workers...');
            $this->call('queue:restart');

            // 7. Bring app back up
            $this->call('up');

            $this->info('✅ Server deployment complete. Tenants can now install their updates.');
            Log::info('system:deploy completed successfully.');

            return 0;

        } catch (\Throwable $e) {
            $this->call('up'); // Always lift maintenance mode
            $this->error('❌ Deployment failed: ' . $e->getMessage());
            Log::error('system:deploy failed: ' . $e->getMessage());
            return 1;
        }
    }

    private function exec(string $command): void
    {
        $output = shell_exec($command . ' 2>&1');
        $this->line($output);

        if ($output === null) {
            throw new \RuntimeException("Command failed: {$command}");
        }
    }
}