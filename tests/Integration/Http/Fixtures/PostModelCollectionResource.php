<?php

namespace WpStarter\Tests\Integration\Http\Fixtures;

use WpStarter\Http\Resources\Json\ResourceCollection;

class PostModelCollectionResource extends ResourceCollection
{
    public $collects = Post::class;

    public function toArray($request)
    {
        return ['data' => $this->collection];
    }
}
