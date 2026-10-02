<?php

namespace WpStarter\Tests\Integration\Broadcasting;

use WpStarter\Broadcasting\BroadcastEvent;
use WpStarter\Broadcasting\BroadcastManager;
use WpStarter\Broadcasting\UniqueBroadcastEvent;
use WpStarter\Config\Repository;
use WpStarter\Container\Container;
use WpStarter\Contracts\Broadcasting\ShouldBeUnique;
use WpStarter\Contracts\Broadcasting\ShouldBroadcast;
use WpStarter\Contracts\Broadcasting\ShouldBroadcastNow;
use WpStarter\Contracts\Broadcasting\ShouldRescue;
use WpStarter\Contracts\Cache\Repository as Cache;
use WpStarter\Support\Facades\Broadcast;
use WpStarter\Support\Facades\Bus;
use WpStarter\Support\Facades\Queue;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use RuntimeException;

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

    public function testEventsCanBeRescued()
    {
        Bus::fake();
        Queue::fake();

        Broadcast::queue(new TestEventRescue);

        Bus::assertNotDispatched(BroadcastEvent::class);
        Queue::assertPushed(BroadcastEvent::class);
    }

    public function testNowEventsCanBeRescued()
    {
        Bus::fake();
        Queue::fake();

        Broadcast::queue(new TestEventNowRescue);

        Bus::assertDispatched(BroadcastEvent::class);
        Queue::assertNotPushed(BroadcastEvent::class);
    }

    public function testUniqueEventsCanBeBroadcast()
    {
        Bus::fake();
        Queue::fake();

        Broadcast::queue(new TestEventUnique);

        Bus::assertNotDispatched(UniqueBroadcastEvent::class);
        Queue::assertPushed(UniqueBroadcastEvent::class);

        $lockKey = 'laravel_unique_job:'.hash('xxh128', TestEventUnique::class).':';
        $this->assertFalse($this->app->get(Cache::class)->lock($lockKey, 10)->get());
    }

    public function testUniqueEventsCanBeBroadcastWithUniqueIdFromProperty()
    {
        Bus::fake();
        Queue::fake();

        Broadcast::queue(new TestEventUniqueWithIdProperty);

        Bus::assertNotDispatched(UniqueBroadcastEvent::class);
        Queue::assertPushed(UniqueBroadcastEvent::class);

        $lockKey = 'laravel_unique_job:'.hash('xxh128', TestEventUniqueWithIdProperty::class).':unique-id-property';
        $this->assertFalse($this->app->get(Cache::class)->lock($lockKey, 10)->get());
    }

    public function testUniqueEventsCanBeBroadcastWithUniqueIdFromMethod()
    {
        Bus::fake();
        Queue::fake();

        Broadcast::queue(new TestEventUniqueWithIdMethod);

        Bus::assertNotDispatched(UniqueBroadcastEvent::class);
        Queue::assertPushed(UniqueBroadcastEvent::class);

        $lockKey = 'laravel_unique_job:'.hash('xxh128', TestEventUniqueWithIdMethod::class).':unique-id-method';
        $this->assertFalse($this->app->get(Cache::class)->lock($lockKey, 10)->get());
    }

    public function testThrowExceptionWhenUnknownStoreIsUsed()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Broadcast connection [alien_connection] is not defined.');

        $userConfig = [
            'broadcasting' => [
                'connections' => [
                    'my_connection' => [
                        'driver' => 'pusher',
                    ],
                ],
            ],
        ];

        $app = $this->getApp($userConfig);

        $broadcastManager = new BroadcastManager($app);

        $broadcastManager->connection('alien_connection');
    }

    public function testThrowExceptionWhenDriverCreationFails()
    {
        $userConfig = [
            'broadcasting' => [
                'connections' => [
                    'log_connection_1' => [
                        'driver' => 'log',
                    ],
                ],
            ],
        ];

        $app = $this->getApp($userConfig);
        $app->singleton(\Psr\Log\LoggerInterface::class, function () {
            throw new \RuntimeException('Logger service not available');
        });

        $broadcastManager = new BroadcastManager($app);

        try {
            $broadcastManager->connection('log_connection_1');
            $this->fail('Expected BroadcastException was not thrown');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString('Failed to create broadcaster for connection "log_connection_1"', $e->getMessage());
            $this->assertStringContainsString('Logger service not available', $e->getMessage());
            $this->assertInstanceOf(\RuntimeException::class, $e->getPrevious());
        }
    }

    protected function getApp(array $userConfig)
    {
        $app = new Container;
        $app->singleton('config', fn () => new Repository($userConfig));

        return $app;
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

class TestEventUnique implements ShouldBroadcast, ShouldBeUnique
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

class TestEventUniqueWithIdProperty extends TestEventUnique
{
    public string $uniqueId = 'unique-id-property';
}

class TestEventUniqueWithIdMethod extends TestEventUnique
{
    public string $uniqueId = 'unique-id-method';
}

class TestEventRescue implements ShouldBroadcast, ShouldRescue
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

class TestEventNowRescue implements ShouldBroadcastNow, ShouldRescue
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
