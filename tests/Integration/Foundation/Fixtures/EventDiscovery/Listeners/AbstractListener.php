<?php

namespace WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners;

use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;

abstract class AbstractListener
{
    abstract public function handle(EventOne $event);
}
