<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="first_name", type="string", example="John"),
 *     @OA\Property(property="last_name", type="string", example="Doe"),
 *     @OA\Property(property="full_name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *     @OA\Property(property="phone", type="string", nullable=true, example="+1234567890"),
 *     @OA\Property(property="role", type="string", enum={"admin","merchant","customer"}, example="customer"),
 *     @OA\Property(property="status", type="string", enum={"active","inactive","suspended"}, example="active"),
 *     @OA\Property(property="email_verified", type="boolean", example=false),
 *     @OA\Property(property="phone_verified", type="boolean", example=false),
 *     @OA\Property(property="avatar_url", type="string", nullable=true, example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-09T22:06:39.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-09T22:06:39.000000Z")
 * )
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status,
            'email_verified' => $this->email_verified,
            'phone_verified' => $this->phone_verified,
            'avatar_url' => $this->avatar_url,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            
            // Only include sensitive data if the user is viewing their own profile
            'login_attempts' => $this->when($request->user()?->id === $this->id, $this->login_attempts),
            'last_failed_login_at' => $this->when($request->user()?->id === $this->id, $this->last_failed_login_at?->toISOString()),
        ];
    }
}
