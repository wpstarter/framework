<?php

namespace WpStarter\Tests\Support;

use WpStarter\Cache\CacheManager;
use WpStarter\Cache\Events\CacheMissed;
use WpStarter\Config\Repository as ConfigRepository;
use WpStarter\Container\Container;
use WpStarter\Contracts\Events\Dispatcher as DispatcherContract;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Events\Dispatcher;
use WpStarter\Support\Facades\Cache;
use WpStarter\Support\Facades\Event;
use WpStarter\Support\Facades\Facade;
use WpStarter\Support\Testing\Fakes\EventFake;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class SupportFacadesEventTest extends TestCase
{
    private $events;

    protected function setUp(): void
    {
        parent::setUp();

        $this->events = m::mock(Dispatcher::class);

        $container = new Container;
        $container->instance('events', $this->events);
        $container->alias('events', DispatcherContract::class);
        $container->instance('cache', new CacheManager($container));
        $container->instance('config', new ConfigRepository($this->getCacheConfig()));

        Facade::setFacadeApplication($container);
    }

    protected function tearDown(): void
    {
        Event::clearResolvedInstances();
        Event::setFacadeApplication(null);

        m::close();
    }

    public function testFakeFor()
    {
        Event::fakeFor(function () {
            (new FakeForStub)->dispatch();

            Event::assertDispatched(EventStub::class);
        });

        $this->events->shouldReceive('dispatch')->once();

        (new FakeForStub)->dispatch();
    }

    public function testFakeForSwapsDispatchers()
    {
        $arrayRepository = Cache::store('array');

        Event::fakeFor(function () use ($arrayRepository) {
            $this->assertInstanceOf(EventFake::class, Event::getFacadeRoot());
            $this->assertInstanceOf(EventFake::class, Model::getEventDispatcher());
            $this->assertInstanceOf(EventFake::class, $arrayRepository->getEventDispatcher());
        });

        $this->assertSame($this->events, Event::getFacadeRoot());
        $this->assertSame($this->events, Model::getEventDispatcher());
        $this->assertSame($this->events, $arrayRepository->getEventDispatcher());
    }

    public function testFakeSwapsDispatchersInResolvedCacheRepositories()
    {
        $arrayRepository = Cache::store('array');

        $this->events->shouldReceive('dispatch')->once();
        $arrayRepository->get('foo');

        Event::fake();

        $arrayRepository->get('bar');

        Event::assertDispatched(CacheMissed::class);
    }

    protected function getCacheConfig()
    {
        return [
            'cache' => [
                'stores' => [
                    'array' => [
                        'driver' => 'array',
                    ],
                ],
            ],
        ];
    }
}

class FakeForStub
{
    public function dispatch()
    {
        Event::dispatch(EventStub::class);
    }
}
