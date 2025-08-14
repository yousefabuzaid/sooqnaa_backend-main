<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Backward compatibility routes (direct controller calls)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])
        ->name('auth.register.legacy')
        ->middleware('throttle:5,1');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('auth.login.legacy')
        ->middleware('throttle:5,1');

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
        ->name('auth.forgot-password.legacy')
        ->middleware('throttle:3,1');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('auth.reset-password.legacy')
        ->middleware('throttle:3,1');

    Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])
        ->name('auth.verify-email.legacy');

    Route::get('/verify-phone/{token}', [AuthController::class, 'verifyPhone'])
        ->name('auth.verify-phone.legacy');

    Route::post('/send-otp', [AuthController::class, 'sendOTP'])
        ->name('auth.send-otp.legacy')
        ->middleware('throttle:3,1');

    Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])
        ->name('auth.verify-otp.legacy')
        ->middleware('throttle:5,1');
});

// Legacy protected routes (direct controller calls)
Route::middleware('auth:sanctum')->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('auth.logout.legacy');

    Route::post('/refresh', [AuthController::class, 'refresh'])
        ->name('auth.refresh.legacy');

    Route::get('/me', [AuthController::class, 'me'])
        ->name('auth.me.legacy');
});

// Legacy profile routes (direct controller calls)
Route::middleware('auth:sanctum')->prefix('profile')->group(function () {
    Route::get('/', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
        ]);
    })->name('profile.show.legacy');

    Route::put('/', function (Request $request) {
        $request->validate([
            'first_name' => 'sometimes|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'sometimes|string|max:100|regex:/^[a-zA-Z\s]+$/',
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $request->user()->id . '|regex:/^[\+]?[1-9][\d]{0,15}$/',
            'avatar_url' => 'sometimes|url',
        ]);

        $request->user()->update($request->only([
            'first_name', 'last_name', 'phone', 'avatar_url'
        ]));

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $request->user()->fresh(),
        ]);
    })->name('profile.update.legacy');
});

// Legacy health check (direct controller call)
Route::get('/health', [HealthController::class, 'check'])
    ->name('health.legacy');

// API Version 1 Routes
Route::prefix('v1')->group(function () {
    
    // Handle CORS preflight requests
    Route::options('{any}', function () {
        return response()->json([], 200);
    })->where('any', '.*');

    // Public routes (no authentication required)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])
            ->name('auth.register')
            ->middleware('throttle:5,1'); // 5 attempts per minute

        Route::post('/login', [AuthController::class, 'login'])
            ->name('auth.login')
            ->middleware('throttle:5,1'); // 5 attempts per minute

        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])
            ->name('auth.forgot-password')
            ->middleware('throttle:3,1'); // 3 attempts per minute

        Route::post('/reset-password', [AuthController::class, 'resetPassword'])
            ->name('auth.reset-password')
            ->middleware('throttle:3,1'); // 3 attempts per minute

        Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail'])
            ->name('auth.verify-email');

        Route::get('/verify-phone/{token}', [AuthController::class, 'verifyPhone'])
            ->name('auth.verify-phone');

        Route::post('/send-otp', [AuthController::class, 'sendOTP'])
            ->name('auth.send-otp')
            ->middleware('throttle:3,1'); // 3 attempts per minute

        Route::post('/verify-otp', [AuthController::class, 'verifyOTP'])
            ->name('auth.verify-otp')
            ->middleware('throttle:5,1'); // 5 attempts per minute
    });

    // Protected routes (authentication required)
    Route::middleware('auth:sanctum')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout'])
                ->name('auth.logout');

            Route::post('/refresh', [AuthController::class, 'refresh'])
                ->name('auth.refresh');

            Route::get('/me', [AuthController::class, 'me'])
                ->name('auth.me');
        });

        // User profile routes
        Route::prefix('profile')->group(function () {
            Route::get('/', function (Request $request) {
                return response()->json([
                    'user' => $request->user(),
                ]);
            })->name('profile.show');

            Route::put('/', function (Request $request) {
                $request->validate([
                    'first_name' => 'sometimes|string|max:100|regex:/^[a-zA-Z\s]+$/',
                    'last_name' => 'sometimes|string|max:100|regex:/^[a-zA-Z\s]+$/',
                    'phone' => 'sometimes|string|max:20|unique:users,phone,' . $request->user()->id . '|regex:/^[\+]?[1-9][\d]{0,15}$/',
                    'avatar_url' => 'sometimes|url',
                ]);

                $request->user()->update($request->only([
                    'first_name', 'last_name', 'phone', 'avatar_url'
                ]));

                return response()->json([
                    'message' => 'Profile updated successfully',
                    'user' => $request->user()->fresh(),
                ]);
            })->name('profile.update');
        });
    });

    // Health check route
    Route::get('/health', [HealthController::class, 'check'])->name('health');
});

// Fallback route for undefined API endpoints
Route::fallback(function () {
    return response()->json([
        'message' => 'API endpoint not found',
        'error' => 'Not Found',
        'version' => 'v1',
        'available_endpoints' => [
            'v1' => '/api/v1/*',
            'legacy' => '/api/auth/* (deprecated, use v1)',
        ],
    ], 404);
});
