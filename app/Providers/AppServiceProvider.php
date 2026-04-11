<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Custom Blade directive to check tenant's plan
        \Blade::directive('tenantPlan', function ($expression) {
            return "<?php if(optional(tenancy()->tenant)->plan && in_array(optional(tenancy()->tenant)->plan, (array)$expression)): ?>";
        });

        // End the tenantPlan directive
        \Blade::directive('endtenantPlan', function () {
            return "<?php endif; ?>";
        });
    }
}
