<?php

namespace WpStarter\Tests\Integration\Http\Fixtures;

use WpStarter\Http\Resources\Json\ResourceCollection;

class EmptyPostCollectionResource extends ResourceCollection
{
    public $collects = PostResource::class;
}
