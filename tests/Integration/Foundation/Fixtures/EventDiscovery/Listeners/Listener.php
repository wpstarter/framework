<?php

namespace WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners;

use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;
use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventTwo;

class Listener
{
    public function handle(EventOne $event)
    {
        //
    }

    public function handleEventOne(EventOne $event)
    {
        //
    }

    public function handleEventTwo(EventTwo $event)
    {
        //
    }
}
