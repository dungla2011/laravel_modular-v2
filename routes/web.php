<?php

use App\Http\Controllers\HealthController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Health check routes
Route::get('/health', [HealthController::class, 'index'])->name('health');
Route::get('/health/live', [HealthController::class, 'live'])->name('health.live');
Route::get('/health/ready', [HealthController::class, 'ready'])->name('health.ready');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-mongo', function () {
    try {
        // Test tạo user mới
        $user = User::create([
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Lấy tất cả users
        $users = User::all();

        return response()->json([
            'status'       => 'success',
            'message'      => 'MongoDB connected successfully!',
            'created_user' => $user,
            'total_users'  => $users->count(),
            'all_users'    => $users,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'MongoDB connection failed: ' . $e->getMessage(),
        ]);
    }
});
