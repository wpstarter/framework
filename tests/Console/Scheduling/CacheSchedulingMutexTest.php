<?php

namespace WpStarter\Tests\Console\Scheduling;

use WpStarter\Console\Scheduling\CacheEventMutex;
use WpStarter\Console\Scheduling\CacheSchedulingMutex;
use WpStarter\Console\Scheduling\Event;
use WpStarter\Contracts\Cache\Factory;
use WpStarter\Contracts\Cache\Repository;
use WpStarter\Support\Carbon;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class CacheSchedulingMutexTest extends TestCase
{
    /**
     * @var \WpStarter\Console\Scheduling\CacheSchedulingMutex
     */
    protected $cacheMutex;

    /**
     * @var \WpStarter\Console\Scheduling\Event
     */
    protected $event;

    /**
     * @var \WpStarter\Support\Carbon
     */
    protected $time;

    /**
     * @var \WpStarter\Contracts\Cache\Factory
     */
    protected $cacheFactory;

    /**
     * @var \WpStarter\Contracts\Cache\Repository
     */
    protected $cacheRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheFactory = m::mock(Factory::class);
        $this->cacheRepository = m::mock(Repository::class);
        $this->cacheFactory->shouldReceive('store')->andReturn($this->cacheRepository);
        $this->cacheMutex = new CacheSchedulingMutex($this->cacheFactory);
        $this->event = new Event(new CacheEventMutex($this->cacheFactory), 'command');
        $this->time = Carbon::now();
    }

    public function testMutexReceivesCorrectCreate()
    {
        $this->cacheRepository->shouldReceive('add')->once()->with($this->event->mutexName().$this->time->format('Hi'), true, 3600)->andReturn(true);

        $this->assertTrue($this->cacheMutex->create($this->event, $this->time));
    }

    public function testCanUseCustomConnection()
    {
        $this->cacheFactory->shouldReceive('store')->with('test')->andReturn($this->cacheRepository);
        $this->cacheRepository->shouldReceive('add')->once()->with($this->event->mutexName().$this->time->format('Hi'), true, 3600)->andReturn(true);
        $this->cacheMutex->useStore('test');

        $this->assertTrue($this->cacheMutex->create($this->event, $this->time));
    }

    public function testPreventsMultipleRuns()
    {
        $this->cacheRepository->shouldReceive('add')->once()->with($this->event->mutexName().$this->time->format('Hi'), true, 3600)->andReturn(false);

        $this->assertFalse($this->cacheMutex->create($this->event, $this->time));
    }

    public function testChecksForNonRunSchedule()
    {
        $this->cacheRepository->shouldReceive('has')->once()->with($this->event->mutexName().$this->time->format('Hi'))->andReturn(false);

        $this->assertFalse($this->cacheMutex->exists($this->event, $this->time));
    }

    public function testChecksForAlreadyRunSchedule()
    {
        $this->cacheRepository->shouldReceive('has')->with($this->event->mutexName().$this->time->format('Hi'))->andReturn(true);

        $this->assertTrue($this->cacheMutex->exists($this->event, $this->time));
    }
}
