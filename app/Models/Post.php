<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory,SoftDeletes;


    protected $fillable = [
        'title',
        'body',
        'cover_image',
        'pinned'
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'pinned' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Get the user that owns the post
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tags associated with the post
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class,'post_tag');
    }

    /**
     * Scope for getting pinned posts first
     */
    public function scopePinnedFirst(Builder $query): Builder
    {
        return $query->orderBy('pinned', 'desc')
                     ->orderBy('created_at', 'desc');
    }

    /**
     * Scope for getting user's posts
     */
    public function scopeForUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}
