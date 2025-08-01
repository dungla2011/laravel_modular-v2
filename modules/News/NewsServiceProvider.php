<?php

namespace Modules\News;

use Illuminate\Support\ServiceProvider;
use Modules\News\Repositories\NewsRepository;
use Modules\News\Repositories\CategoryRepository;
use Modules\News\Services\NewsService;
use Modules\News\Services\CategoryService;

class NewsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register repositories
        $this->app->singleton(NewsRepository::class, function ($app) {
            return new NewsRepository($app->make(\Modules\News\Models\News::class));
        });

        $this->app->singleton(CategoryRepository::class, function ($app) {
            return new CategoryRepository($app->make(\Modules\News\Models\Category::class));
        });

        // Register services
        $this->app->singleton(NewsService::class, function ($app) {
            return new NewsService($app->make(NewsRepository::class));
        });

        $this->app->singleton(CategoryService::class, function ($app) {
            return new CategoryService($app->make(CategoryRepository::class));
        });
    }

    public function boot(): void
    {
        // Register routes
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        
        // Load views if needed
        // $this->loadViewsFrom(__DIR__ . '/Views', 'news');
        
        // Load migrations if needed
        // $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
    }
}
