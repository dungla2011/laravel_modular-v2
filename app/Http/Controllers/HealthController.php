<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function index(): JsonResponse
    {
        $checks = [
            'app'      => $this->checkApp(),
            'database' => $this->checkDatabase(),
            'cache'    => $this->checkCache(),
            'storage'  => $this->checkStorage(),
        ];

        $allHealthy = collect($checks)->every(fn ($check) => $check['status'] === 'ok');

        return response()->json([
            'status'      => $allHealthy ? 'healthy' : 'unhealthy',
            'timestamp'   => now()->toISOString(),
            'checks'      => $checks,
            'version'     => config('app.version', '1.0.0'),
            'environment' => app()->environment(),
        ], $allHealthy ? 200 : 503);
    }

    public function live(): JsonResponse
    {
        return response()->json([
            'status'    => 'alive',
            'timestamp' => now()->toISOString(),
        ]);
    }

    public function ready(): JsonResponse
    {
        $ready = $this->checkDatabase()['status'] === 'ok';

        return response()->json([
            'status'    => $ready ? 'ready' : 'not ready',
            'timestamp' => now()->toISOString(),
        ], $ready ? 200 : 503);
    }

    private function checkApp(): array
    {
        try {
            $status = 'ok';
            $message = 'Application is running';

            // Check if app key is set
            if (empty(config('app.key'))) {
                $status = 'error';
                $message = 'Application key not set';
            }

            return compact('status', 'message');
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    private function checkDatabase(): array
    {
        try {
            // Try to make a simple query
            DB::connection()->getPdo();

            // For MongoDB, try a simple ping
            if (config('database.default') === 'mongodb') {
                DB::connection()->getMongoClient()->selectDatabase(config('database.connections.mongodb.database'))->command(['ping' => 1]);
            }

            return [
                'status'  => 'ok',
                'message' => 'Database connection successful',
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }
    }

    private function checkCache(): array
    {
        try {
            $key = 'health_check_' . time();
            $value = 'test_value';

            Cache::put($key, $value, 60);
            $retrieved = Cache::get($key);
            Cache::forget($key);

            if ($retrieved === $value) {
                return [
                    'status'  => 'ok',
                    'message' => 'Cache is working',
                ];
            }

            return [
                'status'  => 'error',
                'message' => 'Cache test failed',
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Cache error: ' . $e->getMessage(),
            ];
        }
    }

    private function checkStorage(): array
    {
        try {
            $testFile = 'health_check_' . time() . '.txt';
            $testContent = 'Health check test';

            Storage::put($testFile, $testContent);
            $retrieved = Storage::get($testFile);
            Storage::delete($testFile);

            if ($retrieved === $testContent) {
                return [
                    'status'  => 'ok',
                    'message' => 'Storage is working',
                ];
            }

            return [
                'status'  => 'error',
                'message' => 'Storage test failed',
            ];
        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'message' => 'Storage error: ' . $e->getMessage(),
            ];
        }
    }
}
