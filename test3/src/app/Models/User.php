<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Laravel\Sanctum\HasApiTokens;

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
