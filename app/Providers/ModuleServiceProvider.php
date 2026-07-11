<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

class ModuleServiceProvider extends ServiceProvider
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
        $modulesPath = app_path('Modules');

        if (!File::exists($modulesPath)) {
            return;
        }

        $modules = File::directories($modulesPath);

        foreach ($modules as $modulePath) {
            $moduleName = basename($modulePath);

            // Register Views with namespace (e.g. view('Authentication::login'))
            $viewsPath = $modulePath . '/Views';
            if (File::exists($viewsPath)) {
                $this->loadViewsFrom($viewsPath, $moduleName);
            }

            // Register Migrations
            $migrationsPath = $modulePath . '/Database/Migrations';
            if (File::exists($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);
            }

            // Register Routes
            $routesPath = $modulePath . '/Routes';
            if (File::exists($routesPath)) {
                $webRoutes = $routesPath . '/web.php';
                $apiRoutes = $routesPath . '/api.php';

                if (File::exists($webRoutes)) {
                    Route::middleware('web')
                        ->group($webRoutes);
                }

                if (File::exists($apiRoutes)) {
                    Route::prefix('api')
                        ->middleware('api')
                        ->group($apiRoutes);
                }
            }
        }
    }
}
