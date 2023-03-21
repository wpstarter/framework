<?php

namespace WpStarter\Tests\Integration\Http\Fixtures;

class PostResourceWithExtraData extends PostResource
{
    public function with($request)
    {
        return ['foo' => 'bar'];
    }
}
