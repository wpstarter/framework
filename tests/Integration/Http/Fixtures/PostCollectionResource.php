<?php

namespace WpStarter\Tests\Integration\Http\Fixtures;

use WpStarter\Http\Resources\Json\ResourceCollection;

class PostCollectionResource extends ResourceCollection
{
    public $collects = PostResource::class;

    public function toArray($request)
    {
        return ['data' => $this->collection];
    }
}
