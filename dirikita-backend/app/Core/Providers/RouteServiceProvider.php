<?php

namespace App\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
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
        $this->mapModuleRoutes();
    }

    /**
     * Map module routes.
     */
    protected function mapModuleRoutes(): void
    {
        // Load User module routes
        if (file_exists(base_path('app/Modules/User/Routes/api.php'))) {
            Route::middleware('web')
                ->group(base_path('app/Modules/User/Routes/api.php'));
        }

        // Load Auth module routes (if they exist)
        if (file_exists(base_path('app/Modules/Auth/Routes/api.php'))) {
            Route::middleware('web')
                ->group(base_path('app/Modules/Auth/Routes/api.php'));
        }

        // Add more modules here as needed
    }
}

