<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'token',
        'type',
        'ip_address',
        'expires_at',
        'used',
        'used_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the verification token.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the token is valid.
     */
    public function isValid(): bool
    {
        return !$this->used && $this->expires_at->isFuture();
    }

    /**
     * Mark the token as used.
     */
    public function markAsUsed(): void
    {
        $this->update([
            'used' => true,
            'used_at' => now(),
        ]);
    }

    /**
     * Check if the token is for email verification.
     */
    public function isEmailVerification(): bool
    {
        return $this->type === 'email';
    }

    /**
     * Check if the token is for phone verification.
     */
    public function isPhoneVerification(): bool
    {
        return $this->type === 'phone';
    }

    /**
     * Check if the token is for password reset.
     */
    public function isPasswordReset(): bool
    {
        return $this->type === 'password_reset';
    }

    /**
     * Scope a query to only include valid tokens.
     */
    public function scopeValid($query)
    {
        return $query->where('used', false)->where('expires_at', '>', now());
    }

    /**
     * Scope a query to only include used tokens.
     */
    public function scopeUsed($query)
    {
        return $query->where('used', true);
    }

    /**
     * Scope a query to only include expired tokens.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope a query to only include tokens by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
