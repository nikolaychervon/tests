<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property string $content
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $commentable_type
 * @property int $commentable_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read User $user
 * @property-read News|Video $commentable
 * @property-read Comment|null $parent
 * @property-read Collection<int, Comment> $replies
 * @property-read int|null $replies_count
 * @property-read int $depth
 */
class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'parent_id',
        'commentable_type',
        'commentable_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function repliesRecursive(): Builder|HasMany
    {
        return $this->replies()->with('repliesRecursive', 'user');
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeWithUser($query)
    {
        return $query->with('user');
    }

    public function scopeWithReplies($query)
    {
        return $query->with(['replies' => function ($query) {
            $query->withUser()->withReplies();
        }]);
    }

    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }
}
