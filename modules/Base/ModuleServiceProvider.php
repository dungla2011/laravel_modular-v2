<?php

namespace Modules\Base;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerModules();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadModuleRoutes();
        $this->loadModuleViews();
        $this->loadModuleMigrations();
    }

    /**
     * Register all modules.
     */
    protected function registerModules(): void
    {
        $modulesPath = base_path('modules');

        if (File::isDirectory($modulesPath)) {
            $modules = File::directories($modulesPath);

            foreach ($modules as $modulePath) {
                $moduleName = basename($modulePath);

                // Skip Base module
                if ($moduleName === 'Base') {
                    continue;
                }

                $providerPath = $modulePath . '/' . $moduleName . 'ServiceProvider.php';

                if (File::exists($providerPath)) {
                    $providerClass = "Modules\\{$moduleName}\\{$moduleName}ServiceProvider";

                    if (class_exists($providerClass)) {
                        $this->app->register($providerClass);
                    }
                }
            }
        }
    }

    /**
     * Load module routes.
     */
    protected function loadModuleRoutes(): void
    {
        $modulesPath = base_path('modules');

        if (File::isDirectory($modulesPath)) {
            $modules = File::directories($modulesPath);

            foreach ($modules as $modulePath) {
                $moduleName = basename($modulePath);

                // Skip Base module
                if ($moduleName === 'Base') {
                    continue;
                }

                $routesPath = $modulePath . '/Routes';

                if (File::isDirectory($routesPath)) {
                    // Load web routes
                    $webRoutes = $routesPath . '/web.php';
                    if (File::exists($webRoutes)) {
                        $this->loadRoutesFrom($webRoutes);
                    }

                    // Load API routes
                    $apiRoutes = $routesPath . '/api.php';
                    if (File::exists($apiRoutes)) {
                        $this->loadRoutesFrom($apiRoutes);
                    }

                    // Load admin routes
                    $adminRoutes = $routesPath . '/admin.php';
                    if (File::exists($adminRoutes)) {
                        $this->loadRoutesFrom($adminRoutes);
                    }
                }
            }
        }
    }

    /**
     * Load module views.
     */
    protected function loadModuleViews(): void
    {
        $modulesPath = base_path('modules');

        if (File::isDirectory($modulesPath)) {
            $modules = File::directories($modulesPath);

            foreach ($modules as $modulePath) {
                $moduleName = basename($modulePath);

                // Skip Base module
                if ($moduleName === 'Base') {
                    continue;
                }

                $viewsPath = $modulePath . '/Views';

                if (File::isDirectory($viewsPath)) {
                    $this->loadViewsFrom($viewsPath, strtolower($moduleName));
                }
            }
        }
    }

    /**
     * Load module migrations.
     */
    protected function loadModuleMigrations(): void
    {
        $modulesPath = base_path('modules');

        if (File::isDirectory($modulesPath)) {
            $modules = File::directories($modulesPath);

            foreach ($modules as $modulePath) {
                $moduleName = basename($modulePath);

                // Skip Base module
                if ($moduleName === 'Base') {
                    continue;
                }

                $migrationsPath = $modulePath . '/Database/Migrations';

                if (File::isDirectory($migrationsPath)) {
                    $this->loadMigrationsFrom($migrationsPath);
                }
            }
        }
    }
}
