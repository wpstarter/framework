<?php

namespace WpStarter\Tests\Integration\Http\Resources\JsonApi\Fixtures;

use WpStarter\Http\Resources\JsonApi\JsonApiResource;

class CommentResource extends JsonApiResource
{
    /**
     * The resource's attributes.
     */
    public $attributes = [
        'content',
    ];

    /**
     * The resource's relationships.
     */
    public $relationships = [
        'posts',
        'commenter' => UserApiResource::class,
    ];
}
