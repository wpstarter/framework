<?php

namespace WpStarter\Tests\Integration\Http\Fixtures;

use WpStarter\Http\Resources\Json\JsonResource;

class SerializablePostResource extends JsonResource
{
    public function toArray($request)
    {
        return new JsonSerializableResource($this);
    }
}
