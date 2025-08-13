<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password_hash',
        'role',
        'status',
        'email_verified',
        'phone_verified',
        'login_attempts',
        'last_failed_login_at',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified' => 'boolean',
            'phone_verified' => 'boolean',
            'login_attempts' => 'integer',
            'last_failed_login_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the password attribute name for authentication.
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Check if user can login.
     */
    public function canLogin(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Increment login attempts.
     */
    public function incrementLoginAttempts(): void
    {
        $this->increment('login_attempts');
        $this->update(['last_failed_login_at' => Carbon::now()]);
    }

    /**
     * Reset login attempts.
     */
    public function resetLoginAttempts(): void
    {
        $this->update([
            'login_attempts' => 0,
            'last_failed_login_at' => null,
        ]);
    }

    /**
     * Get full name attribute.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Relationship: User has many sessions.
     */
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    /**
     * Relationship: User has many verification tokens.
     */
    public function verificationTokens()
    {
        return $this->hasMany(VerificationToken::class);
    }
}
