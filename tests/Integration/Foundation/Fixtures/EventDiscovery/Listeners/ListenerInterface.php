<?php

namespace WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners;

use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;

interface ListenerInterface
{
    public function handle(EventOne $event);
}
