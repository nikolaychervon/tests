<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "User",
    title: "User",
    description: "User model",
    required: ["id", "login", "created_at"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "login", type: "string", example: "Test_login"),
        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-01-16 09:21"),
    ]
)]
#[OA\Schema(
    schema: "Auth",
    title: "Auth Data",
    description: "Data for authorization",
    required: ["login", "password"],
    properties: [
        new OA\Property(property: "login", description: "Логин", type: "string", example: "testuser"),
        new OA\Property(property: "password", description: "Пароль", type: "string", format: "password", example: "password")
    ]
)]
/**
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 */
class User extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'login',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
