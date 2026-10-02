<?php

namespace WpStarter\Tests\Integration\Http\Resources\JsonApi\Fixtures;

use WpStarter\Database\Eloquent\Attributes\UseFactory;
use WpStarter\Database\Eloquent\Attributes\UseResource;
use WpStarter\Database\Eloquent\Factories\HasFactory;
use WpStarter\Database\Eloquent\Model;

#[UseResource(ProfileResource::class)]
#[UseFactory(ProfileFactory::class)]
class Profile extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
