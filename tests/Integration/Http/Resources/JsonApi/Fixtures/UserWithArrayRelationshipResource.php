<?php

namespace WpStarter\Tests\Integration\Http\Resources\JsonApi\Fixtures;

use WpStarter\Http\Request;
use WpStarter\Http\Resources\JsonApi\JsonApiResource;

class UserWithArrayRelationshipResource extends JsonApiResource
{
    public function toType(Request $request)
    {
        return 'users';
    }

    public function toAttributes(Request $request)
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
