<?php

use Illuminate\Support\Facades\Route;
use Modules\News\Controllers\NewsController;
use Modules\News\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| News Module API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api/v1')->group(function () {
    
    // Public News Routes
    Route::prefix('news')->group(function () {
        Route::get('published', [NewsController::class, 'published']);
        Route::get('featured', [NewsController::class, 'featured']);
        Route::get('popular', [NewsController::class, 'popular']);
        Route::get('archive/{year}/{month}', [NewsController::class, 'archive']);
        Route::get('slug/{slug}', [NewsController::class, 'showBySlug']);
        Route::get('{id}/related', [NewsController::class, 'related']);
    });

    // Public Category Routes
    Route::prefix('categories')->group(function () {
        Route::get('active', [CategoryController::class, 'active']);
        Route::get('tree', [CategoryController::class, 'tree']);
        Route::get('with-news-count', [CategoryController::class, 'withNewsCount']);
        Route::get('slug/{slug}', [CategoryController::class, 'showBySlug']);
    });

    // Admin News Routes (Protected by middleware)
    Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
        
        // News Management
        Route::prefix('news')->group(function () {
            Route::get('/', [NewsController::class, 'index']);
            Route::post('/', [NewsController::class, 'store']);
            Route::get('{id}', [NewsController::class, 'show']);
            Route::put('{id}', [NewsController::class, 'update']);
            Route::delete('{id}', [NewsController::class, 'destroy']);
            Route::post('{id}/toggle-featured', [NewsController::class, 'toggleFeatured']);
            Route::delete('bulk', [NewsController::class, 'bulkDestroy']);
            Route::post('bulk/toggle-status', [NewsController::class, 'toggleStatus']);
        });

        // Category Management
        Route::prefix('categories')->group(function () {
            Route::get('/', [CategoryController::class, 'index']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::get('{id}', [CategoryController::class, 'show']);
            Route::put('{id}', [CategoryController::class, 'update']);
            Route::delete('{id}', [CategoryController::class, 'destroy']);
            Route::delete('bulk', [CategoryController::class, 'bulkDestroy']);
            Route::post('bulk/toggle-status', [CategoryController::class, 'toggleStatus']);
        });
    });
});
