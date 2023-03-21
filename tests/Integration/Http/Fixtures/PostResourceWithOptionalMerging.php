<?php

namespace WpStarter\Tests\Integration\Http\Fixtures;

use WpStarter\Http\Resources\Json\JsonResource;

class PostResourceWithOptionalMerging extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            $this->mergeWhen(false, ['first' => 'value']),
            $this->mergeWhen(true, ['second' => 'value']),
        ];
    }
}
