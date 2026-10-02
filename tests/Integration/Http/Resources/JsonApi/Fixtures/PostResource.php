<?php

namespace WpStarter\Tests\Integration\Http\Resources\JsonApi\Fixtures;

use WpStarter\Http\Resources\JsonApi\JsonApiResource;

class PostResource extends JsonApiResource
{
    protected array $attributes = [
        'title',
        'content',
    ];

    protected array $relationships = [
        'author' => AuthorResource::class,
        'comments',
    ];
}
