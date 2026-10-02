<?php

namespace WpStarter\Tests\Integration\Http\Resources\JsonApi\Fixtures;

use WpStarter\Database\Eloquent\Attributes\UseFactory;
use WpStarter\Database\Eloquent\Attributes\UseResource;
use WpStarter\Database\Eloquent\Factories\HasFactory;
use WpStarter\Database\Eloquent\Model;

#[UseFactory(CommentFactory::class)]
#[UseResource(CommentResource::class)]
class Comment extends Model
{
    use HasFactory;

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function commenter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
