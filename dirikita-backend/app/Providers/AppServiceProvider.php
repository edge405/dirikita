<?php

namespace App\Providers;

use App\Core\Providers\RouteServiceProvider;
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
        // Load module routes
        $this->app->register(RouteServiceProvider::class);

        // Register module migration paths
        $this->loadMigrationsFrom([
            app_path('Modules/User/Migrations'),
            app_path('Modules/Auth/Migrations'),
        ]);
    }
}
