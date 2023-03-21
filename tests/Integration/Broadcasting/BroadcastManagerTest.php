<?php

namespace WpStarter\Tests\Integration\Broadcasting;

use WpStarter\Broadcasting\BroadcastEvent;
use WpStarter\Contracts\Broadcasting\ShouldBroadcast;
use WpStarter\Contracts\Broadcasting\ShouldBroadcastNow;
use WpStarter\Support\Facades\Broadcast;
use WpStarter\Support\Facades\Bus;
use WpStarter\Support\Facades\Queue;
use Orchestra\Testbench\TestCase;

class BroadcastManagerTest extends TestCase
{
    public function testEventCanBeBroadcastNow()
    {
        Bus::fake();
        Queue::fake();

        Broadcast::queue(new TestEventNow);

        Bus::assertDispatched(BroadcastEvent::class);
        Queue::assertNotPushed(BroadcastEvent::class);
    }

    public function testEventsCanBeBroadcast()
    {
        Bus::fake();
        Queue::fake();

        Broadcast::queue(new TestEvent);

        Bus::assertNotDispatched(BroadcastEvent::class);
        Queue::assertPushed(BroadcastEvent::class);
    }
}

class TestEvent implements ShouldBroadcast
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \WpStarter\Broadcasting\Channel|\WpStarter\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        //
    }
}

class TestEventNow implements ShouldBroadcastNow
{
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \WpStarter\Broadcasting\Channel|\WpStarter\Broadcasting\Channel[]
     */
    public function broadcastOn()
    {
        //
    }
}
