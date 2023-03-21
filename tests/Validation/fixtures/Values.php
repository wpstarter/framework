<?php

namespace WpStarter\Tests\Validation\fixtures;

use WpStarter\Contracts\Support\Arrayable;

class Values implements Arrayable
{
    public function toArray()
    {
        return [1, 2, 3, 4];
    }
}
