<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     operationId="registerUserLegacy",
     *     tags={"Authentication (Legacy)"},
     *     summary="Register a new user (Legacy endpoint)",
     *     description="Register a new user account with email and password (Legacy endpoint - use /api/v1/auth/register)",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration data",
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","password_confirmation"},
     *             @OA\Property(property="first_name", type="string", maxLength=100, example="John"),
     *             @OA\Property(property="last_name", type="string", maxLength=100, example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="+1234567890"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8, example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"admin","merchant","customer"}, example="customer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully. Please check your email for verification."),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registration failed. Please try again."),
     *             @OA\Property(property="error", type="string", nullable=true)
     *         )
     *     )
     * )
     * 
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     summary="Register a new user",
     *     description="Register a new user account with email and password",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration data",
     *         @OA\JsonContent(
     *             required={"first_name","last_name","email","password","password_confirmation"},
     *             @OA\Property(property="first_name", type="string", maxLength=100, example="John"),
     *             @OA\Property(property="last_name", type="string", maxLength=100, example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="+1234567890"),
     *             @OA\Property(property="password", type="string", format="password", minLength=8, example="password123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"admin","merchant","customer"}, example="customer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully. Please check your email for verification."),
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registration failed. Please try again."),
     *             @OA\Property(property="error", type="string", nullable=true)
     *         )
     *     )
     * )
     * 
     * Register a new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            $user = $this->authService->register($request->validated());

            return response()->json([
                'message' => 'User registered successfully. Please check your email for verification.',
                'user' => new UserResource($user),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Registration failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     summary="Authenticate user",
     *     description="Login user with email and password",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User login credentials",
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@sooqnaa.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGc..."),
     *             @OA\Property(property="token_type", type="string", example="Bearer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Authentication failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     * 
     * Login user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login(
                $request->email,
                $request->password,
                $request->ip(),
                $request->userAgent()
            );

            return response()->json([
                'message' => 'Login successful',
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ]);
        } catch (\Exception $e) {
            Log::warning('Login failed for email: ' . $request->email . ' - ' . $e->getMessage());
            
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Logout failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Logout failed. Please try again.',
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/auth/me",
     *     operationId="getCurrentUser",
     *     tags={"Authentication"},
     *     summary="Get current authenticated user",
     *     description="Retrieve the currently authenticated user's information",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Current user information",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", ref="#/components/schemas/User")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     * 
     * Get authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * Verify email.
     */
    public function verifyEmail(string $token): JsonResponse
    {
        try {
            $user = $this->authService->verifyEmail($token);

            return response()->json([
                'message' => 'Email verified successfully',
                'user' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verify phone.
     */
    public function verifyPhone(string $token): JsonResponse
    {
        try {
            $user = $this->authService->verifyPhone($token);

            return response()->json([
                'message' => 'Phone verified successfully',
                'user' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Send password reset email.
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $this->authService->sendPasswordResetEmail($request->email);

            return response()->json([
                'message' => 'Password reset email sent successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Password reset email failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Failed to send password reset email. Please try again.',
            ], 500);
        }
    }

    /**
     * Reset password.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $user = $this->authService->resetPassword($request->token, $request->password);

            return response()->json([
                'message' => 'Password reset successfully',
                'user' => new UserResource($user),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Send OTP for email verification.
     */
    public function sendOTP(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        try {
            $result = $this->authService->sendEmailOTP($request->email);

            return response()->json([
                'message' => $result['message'],
                'expires_in_minutes' => $result['expires_in_minutes'],
            ]);
        } catch (\Exception $e) {
            Log::error('Send OTP failed for email: ' . $request->email . ' - ' . $e->getMessage());
            
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Verify OTP.
     */
    public function verifyOTP(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6', 'regex:/^[0-9]{6}$/'],
        ]);

        try {
            $result = $this->authService->verifyOTP($request->email, $request->otp);

            return response()->json([
                'message' => $result['message'],
                'user' => new UserResource($result['user']),
            ]);
        } catch (\Exception $e) {
            Log::warning('OTP verification failed for email: ' . $request->email . ' - ' . $e->getMessage());
            
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Refresh token.
     */
    public function refresh(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'message' => 'Unauthenticated',
                ], 401);
            }
            
            $result = $this->authService->refreshToken($user);

            return response()->json([
                'message' => 'Token refreshed successfully',
                'token' => $result['token'],
                'token_type' => $result['token_type'],
            ]);
        } catch (\Exception $e) {
            Log::error('Token refresh failed: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Token refresh failed. Please login again.',
            ], 500);
        }
    }
}
