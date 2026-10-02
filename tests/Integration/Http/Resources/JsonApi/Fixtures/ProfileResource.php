<?php

namespace WpStarter\Tests\Integration\Http\Resources\JsonApi\Fixtures;

use WpStarter\Http\Request;
use WpStarter\Http\Resources\JsonApi\JsonApiResource;

class ProfileResource extends JsonApiResource
{
    protected array $relationships = [
        'user' => UserResource::class,
    ];

    #[\Override]
    public function toAttributes(Request $request)
    {
        return [
            'timezone' => $this->timezone,
            'date_of_birth' => $this->date_of_birth,
        ];
    }
}
