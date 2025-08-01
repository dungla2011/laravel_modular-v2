<?php

namespace Modules\News\Models;

use Modules\Base\Enums\StatusEnum;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'news_comments';

    protected $fillable = [
        'news_id',
        'parent_id',
        'author_name',
        'author_email',
        'author_website',
        'content',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
    ];

    public function news()
    {
        return $this->belongsTo(News::class, 'news_id');
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', StatusEnum::ACTIVE);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
