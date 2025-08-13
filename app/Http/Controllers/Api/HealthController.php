<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/health",
     *     operationId="healthCheck",
     *     tags={"System"},
     *     summary="Health check endpoint",
     *     description="Check the health status of the API and its dependencies",
     *     @OA\Response(
     *         response=200,
     *         description="System is healthy",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="healthy"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="version", type="string", example="1.0.0"),
     *             @OA\Property(property="services", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="System is unhealthy",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="unhealthy"),
     *             @OA\Property(property="timestamp", type="string", format="date-time"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     )
     * )
     */
    public function check(): JsonResponse
    {
        $status = 'healthy';
        $errors = [];
        $services = [];

        // Check database connection
        try {
            DB::connection()->getPdo();
            $services['database'] = 'healthy';
        } catch (\Exception $e) {
            $status = 'unhealthy';
            $errors[] = 'Database connection failed: ' . $e->getMessage();
            $services['database'] = 'unhealthy';
        }

        // Check cache connection
        try {
            Cache::store()->has('health_check');
            $services['cache'] = 'healthy';
        } catch (\Exception $e) {
            $status = 'unhealthy';
            $errors[] = 'Cache connection failed: ' . $e->getMessage();
            $services['cache'] = 'unhealthy';
        }

        // Check storage
        try {
            Storage::disk('local')->exists('health_check');
            $services['storage'] = 'healthy';
        } catch (\Exception $e) {
            $status = 'unhealthy';
            $errors[] = 'Storage connection failed: ' . $e->getMessage();
            $services['storage'] = 'unhealthy';
        }

        // Check application environment
        $services['environment'] = config('app.env');
        $services['debug'] = config('app.debug');

        $response = [
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'version' => config('app.version', '1.0.0'),
            'services' => $services,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status === 'healthy' ? 200 : 503);
    }
}
