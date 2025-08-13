<?php

namespace App\Services;

use App\Models\User;
use App\Models\Session;
use App\Models\VerificationToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthService
{
    /**
     * Register a new user.
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password_hash' => Hash::make($data['password']),
                'role' => $data['role'] ?? 'customer',
                'status' => 'active',
                'email_verified' => false,
                'phone_verified' => false,
            ]);

            // Create email verification token
            $this->createVerificationToken($user, 'email');

            // Send welcome email with verification link
            $this->sendWelcomeEmail($user);

            return $user;
        });
    }

    /**
     * Authenticate user login.
     */
    public function login(string $email, string $password, string $ipAddress, string $userAgent = null): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('Invalid credentials');
        }

        // Check if user can login
        if (!$user->canLogin()) {
            throw new \Exception('Account is not active');
        }

        // Check if account is locked due to too many failed attempts
        if ($this->isAccountLocked($user)) {
            throw new \Exception('Account is temporarily locked due to too many failed login attempts');
        }

        // Verify password
        if (!Hash::check($password, $user->password_hash)) {
            $user->incrementLoginAttempts();
            throw new \Exception('Invalid credentials');
        }

        // Reset login attempts on successful login
        $user->resetLoginAttempts();

        // Create session
        $session = $this->createSession($user, $ipAddress, $userAgent);

        // Create Sanctum token with proper expiration
        $token = $user->createToken('auth-token', ['*'], now()->addDays(30))->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'session_id' => $session->id,
        ];
    }

    /**
     * Logout user.
     */
    public function logout(User $user): void
    {
        // Revoke Sanctum tokens
        $user->tokens()->delete();
        
        // Revoke active sessions
        $user->sessions()->where('revoked', false)->update(['revoked' => true]);
    }

    /**
     * Verify email.
     */
    public function verifyEmail(string $token): User
    {
        $verificationToken = VerificationToken::where('token', $token)
            ->where('type', 'email')
            ->valid()
            ->first();

        if (!$verificationToken) {
            throw new \Exception('Invalid or expired verification token');
        }

        $user = $verificationToken->user;

        DB::transaction(function () use ($user, $verificationToken) {
            $user->update(['email_verified' => true]);
            $verificationToken->markAsUsed();
        });

        return $user;
    }

    /**
     * Verify phone.
     */
    public function verifyPhone(string $token): User
    {
        $verificationToken = VerificationToken::where('token', $token)
            ->where('type', 'phone')
            ->valid()
            ->first();

        if (!$verificationToken) {
            throw new \Exception('Invalid or expired verification token');
        }

        $user = $verificationToken->user;

        DB::transaction(function () use ($user, $verificationToken) {
            $user->update(['phone_verified' => true]);
            $verificationToken->markAsUsed();
        });

        return $user;
    }

    /**
     * Send OTP for email verification.
     */
    public function sendEmailOTP(string $email): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('User not found');
        }

        $otp = $this->generateOTP();
        $this->createOTPToken($user, 'email_otp', $otp);
        $this->sendOTPEmail($user, $otp);

        return [
            'message' => 'OTP sent successfully',
            'expires_in_minutes' => (int) config('auth.otp_expiry_minutes', 10)
        ];
    }

    /**
     * Verify OTP.
     */
    public function verifyOTP(string $email, string $otp, string $type = 'email_otp'): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('User not found');
        }

        $verificationToken = VerificationToken::where('user_id', $user->id)
            ->where('token', $otp)
            ->where('type', $type)
            ->valid()
            ->first();

        if (!$verificationToken) {
            throw new \Exception('Invalid or expired OTP');
        }

        DB::transaction(function () use ($user, $verificationToken, $type) {
            if ($type === 'email_otp') {
                $user->update(['email_verified' => true]);
            }
            $verificationToken->markAsUsed();
        });

        return [
            'message' => 'OTP verified successfully',
            'user' => $user
        ];
    }

    /**
     * Send password reset email.
     */
    public function sendPasswordResetEmail(string $email): void
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Don't reveal if email exists or not
            return;
        }

        $this->createVerificationToken($user, 'password_reset');
        $this->sendPasswordResetEmailMessage($user);
    }

    /**
     * Reset password.
     */
    public function resetPassword(string $token, string $newPassword): User
    {
        $verificationToken = VerificationToken::where('token', $token)
            ->where('type', 'password_reset')
            ->valid()
            ->first();

        if (!$verificationToken) {
            throw new \Exception('Invalid or expired reset token');
        }

        $user = $verificationToken->user;

        DB::transaction(function () use ($user, $verificationToken, $newPassword) {
            $user->update(['password_hash' => Hash::make($newPassword)]);
            $verificationToken->markAsUsed();
        });

        return $user;
    }

    /**
     * Refresh token.
     */
    public function refreshToken(User $user): array
    {
        // Revoke old tokens
        $user->tokens()->delete();
        
        // Create new token
        $token = $user->createToken('auth-token', ['*'], now()->addDays(30))->plainTextToken;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    /**
     * Generate random OTP.
     */
    private function generateOTP(): string
    {
        $length = (int) config('auth.otp_length', 6);
        $otp = '';
        
        for ($i = 0; $i < $length; $i++) {
            $otp .= random_int(0, 9);
        }
        
        return $otp;
    }

    /**
     * Create verification token.
     */
    private function createVerificationToken(User $user, string $type): VerificationToken
    {
        // Revoke existing tokens of the same type
        $user->verificationTokens()
            ->where('type', $type)
            ->where('used', false)
            ->update(['used' => true]);

        return VerificationToken::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'type' => $type,
            'ip_address' => request()->ip(),
            'expires_at' => Carbon::now()->addMinutes((int) config('auth.verification_expiry', 60)),
        ]);
    }

    /**
     * Create OTP token.
     */
    private function createOTPToken(User $user, string $type, string $otp): VerificationToken
    {
        // Revoke existing tokens of the same type
        $user->verificationTokens()
            ->where('type', $type)
            ->where('used', false)
            ->update(['used' => true]);

        return VerificationToken::create([
            'user_id' => $user->id,
            'token' => $otp,
            'type' => $type,
            'ip_address' => request()->ip(),
            'expires_at' => Carbon::now()->addMinutes((int) config('auth.otp_expiry_minutes', 10)),
        ]);
    }

    /**
     * Create session.
     */
    private function createSession(User $user, string $ipAddress, string $userAgent = null): Session
    {
        return Session::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'expires_at' => Carbon::now()->addDays((int) config('auth.session_lifetime_days', 30)), // 30 days session
        ]);
    }

    /**
     * Check if account is locked.
     */
    private function isAccountLocked(User $user): bool
    {
        $maxAttempts = (int) config('auth.max_login_attempts', 5);
        $lockoutMinutes = (int) config('auth.lockout_minutes', 15);

        if ($user->login_attempts >= $maxAttempts) {
            $lastFailedLogin = $user->last_failed_login_at;
            
            if ($lastFailedLogin && $lastFailedLogin->addMinutes($lockoutMinutes)->isFuture()) {
                return true;
            }

            // Reset attempts if lockout period has passed
            $user->resetLoginAttempts();
        }

        return false;
    }

    /**
     * Send welcome email.
     */
    private function sendWelcomeEmail(User $user): void
    {
        try {
            $verificationToken = $user->verificationTokens()
                ->where('type', 'email')
                ->valid()
                ->first();

            if ($verificationToken) {
                Mail::send('emails.welcome', [
                    'user' => $user,
                    'verificationUrl' => url("/api/auth/verify-email/{$verificationToken->token}")
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Welcome to ' . config('app.name'));
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email: ' . $e->getMessage());
        }
    }

    /**
     * Send OTP email.
     */
    private function sendOTPEmail(User $user, string $otp): void
    {
        try {
            Mail::send('emails.otp', [
                'user' => $user,
                'otp' => $otp,
                'expiryMinutes' => (int) config('auth.otp_expiry_minutes', 10)
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your Verification Code - ' . config('app.name'));
            });
        } catch (\Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            throw new \Exception('Failed to send OTP email');
        }
    }

    /**
     * Send password reset email.
     */
    private function sendPasswordResetEmailMessage(User $user): void
    {
        try {
            $verificationToken = $user->verificationTokens()
                ->where('type', 'password_reset')
                ->valid()
                ->first();

            if ($verificationToken) {
                Mail::send('emails.password-reset', [
                    'user' => $user,
                    'resetUrl' => url("/api/auth/reset-password/{$verificationToken->token}")
                ], function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Password Reset Request');
                });
            }
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email: ' . $e->getMessage());
        }
    }
}
