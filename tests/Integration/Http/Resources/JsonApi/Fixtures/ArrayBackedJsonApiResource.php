<?php

namespace WpStarter\Tests\Integration\Http\Resources\JsonApi\Fixtures;

use WpStarter\Http\Request;
use WpStarter\Http\Resources\JsonApi\JsonApiResource;

class ArrayBackedJsonApiResource extends JsonApiResource
{
    public function toId(Request $request)
    {
        return (string) $this->resource['id'];
    }

    public function toType(Request $request)
    {
        return 'things';
    }

    public function toAttributes(Request $request)
    {
        return ['name' => $this->resource['name']];
    }
}
