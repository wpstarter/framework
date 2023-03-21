<?php

namespace WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\UnionListeners;

use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;
use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventTwo;

class UnionListener
{
    public function handle(EventOne|EventTwo $event)
    {
        //
    }
}
