<?php

namespace WpStarter\Tests\Integration\Http\Fixtures;

use WpStarter\Http\Resources\Json\JsonResource;

class ObjectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->first_name,
            'age' => $this->age,
        ];
    }
}
