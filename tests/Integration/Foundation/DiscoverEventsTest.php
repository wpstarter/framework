<?php

namespace WpStarter\Tests\Integration\Foundation;

use WpStarter\Foundation\Events\DiscoverEvents;
use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventOne;
use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Events\EventTwo;
use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\AbstractListener;
use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\Listener;
use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\ListenerInterface;
use WpStarter\Tests\Integration\Foundation\Fixtures\EventDiscovery\UnionListeners\UnionListener;
use Orchestra\Testbench\TestCase;

class DiscoverEventsTest extends TestCase
{
    public function testEventsCanBeDiscovered()
    {
        class_alias(Listener::class, 'Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\Listener');
        class_alias(AbstractListener::class, 'Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\AbstractListener');
        class_alias(ListenerInterface::class, 'Tests\Integration\Foundation\Fixtures\EventDiscovery\Listeners\ListenerInterface');

        $events = DiscoverEvents::within(__DIR__.'/Fixtures/EventDiscovery/Listeners', getcwd());

        $this->assertEquals([
            EventOne::class => [
                Listener::class.'@handle',
                Listener::class.'@handleEventOne',
            ],
            EventTwo::class => [
                Listener::class.'@handleEventTwo',
            ],
        ], $events);
    }

    /**
     * @requires PHP >= 8
     */
    public function testUnionEventsCanBeDiscovered()
    {
        class_alias(UnionListener::class, 'Tests\Integration\Foundation\Fixtures\EventDiscovery\UnionListeners\UnionListener');

        $events = DiscoverEvents::within(__DIR__.'/Fixtures/EventDiscovery/UnionListeners', getcwd());

        $this->assertEquals([
            EventOne::class => [
                UnionListener::class.'@handle',
            ],
            EventTwo::class => [
                UnionListener::class.'@handle',
            ],
        ], $events);
    }
}
