<?php

namespace WpStarter\Tests\Integration\Http\Resources\JsonApi\Fixtures;

use WpStarter\Database\Eloquent\Attributes\UseFactory;
use WpStarter\Database\Eloquent\Attributes\UseResource;
use WpStarter\Database\Eloquent\Factories\HasFactory;
use WpStarter\Foundation\Auth\User as Authenticatable;
use Orchestra\Testbench\Factories\UserFactory;

#[UseResource(UserResource::class)]
#[UseFactory(UserFactory::class)]
class User extends Authenticatable
{
    use HasFactory;

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function chaperonePosts()
    {
        return $this->hasMany(Post::class)->chaperone('author');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps()
            ->using(Membership::class)
            ->as('membership');
    }
}
