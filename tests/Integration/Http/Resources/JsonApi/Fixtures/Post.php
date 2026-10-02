<?php

namespace WpStarter\Tests\Integration\Http\Resources\JsonApi\Fixtures;

use WpStarter\Database\Eloquent\Attributes\UseFactory;
use WpStarter\Database\Eloquent\Attributes\UseResource;
use WpStarter\Database\Eloquent\Factories\HasFactory;
use WpStarter\Database\Eloquent\Model;

#[UseFactory(PostFactory::class)]
#[UseResource(PostResource::class)]
class Post extends Model
{
    use HasFactory;

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
