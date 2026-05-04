<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\SystemVersion;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {   

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            // Remove forceRootUrl entirely
        }
        // Custom Blade directive to check tenant's plan
        Blade::directive('tenantPlan', function ($expression) {
            return "<?php if(optional(tenancy()->tenant)->plan && in_array(optional(tenancy()->tenant)->plan, (array)$expression)): ?>";
        });

        // End the tenantPlan directive
        Blade::directive('endtenantPlan', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('tenantSetting', function ($expression) {
            // Usage: @tenantSetting('brand_name', 'Default Value')
            return "<?php echo \\App\\Models\\TenantSetting::get($expression); ?>";
        });

        View::composer('*', function ($view) {
            try {
                $view->with('currentVersion', SystemVersion::current()?->version);
            } catch (\Throwable $e) {
                $view->with('currentVersion', null);
            }
        });

    }
}
