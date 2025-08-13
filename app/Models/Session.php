<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Session extends Model
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
        'revoked',
        'ip_address',
        'user_agent',
        'last_accessed_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'revoked' => 'boolean',
        'last_accessed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the session is active.
     */
    public function isActive(): bool
    {
        return !$this->revoked && $this->expires_at->isFuture();
    }

    /**
     * Revoke the session.
     */
    public function revoke(): void
    {
        $this->update(['revoked' => true]);
    }

    /**
     * Update the last accessed time.
     */
    public function updateLastAccessed(): void
    {
        $this->update(['last_accessed_at' => now()]);
    }

    /**
     * Scope a query to only include active sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('revoked', false)->where('expires_at', '>', now());
    }

    /**
     * Scope a query to only include expired sessions.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope a query to only include revoked sessions.
     */
    public function scopeRevoked($query)
    {
        return $query->where('revoked', true);
    }
}
