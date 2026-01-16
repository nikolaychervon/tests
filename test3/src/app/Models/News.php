<?php

namespace App\Models;

use App\Traits\HasComments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Collection<int, Comment> $comments
 * @property-read int|null $comments_count
 *
 * @method static withCursorPagination(int $perPage)
 */
class News extends Model
{
    use HasFactory, HasComments;

    protected $fillable = ['title', 'content'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeWithCursorPagination($query, int $perPage = 10)
    {
        return $query->orderBy('id', 'desc')->cursorPaginate($perPage);
    }
}
