<?php

namespace WpStarter\Tests\Integration\Queue;

use Exception;
use WpStarter\Bus\Dispatcher;
use WpStarter\Bus\Queueable;
use WpStarter\Contracts\Queue\Job;
use WpStarter\Foundation\Testing\Concerns\InteractsWithRedis;
use WpStarter\Queue\CallQueuedHandler;
use WpStarter\Queue\InteractsWithQueue;
use WpStarter\Queue\Middleware\ThrottlesExceptionsWithRedis;
use WpStarter\Support\Str;
use Mockery as m;
use Orchestra\Testbench\TestCase;

class ThrottlesExceptionsWithRedisTest extends TestCase
{
    use InteractsWithRedis;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpRedis();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->tearDownRedis();

        m::close();
    }

    public function testCircuitIsOpenedForJobErrors()
    {
        $this->assertJobWasReleasedImmediately(CircuitBreakerWithRedisTestJob::class, $key = Str::random());
        $this->assertJobWasReleasedImmediately(CircuitBreakerWithRedisTestJob::class, $key);
        $this->assertJobWasReleasedWithDelay(CircuitBreakerWithRedisTestJob::class, $key);
    }

    public function testCircuitStaysClosedForSuccessfulJobs()
    {
        $this->assertJobRanSuccessfully(CircuitBreakerWithRedisSuccessfulJob::class, $key = Str::random());
        $this->assertJobRanSuccessfully(CircuitBreakerWithRedisSuccessfulJob::class, $key);
        $this->assertJobRanSuccessfully(CircuitBreakerWithRedisSuccessfulJob::class, $key);
    }

    public function testCircuitResetsAfterSuccess()
    {
        $this->assertJobWasReleasedImmediately(CircuitBreakerWithRedisTestJob::class, $key = Str::random());
        $this->assertJobRanSuccessfully(CircuitBreakerWithRedisSuccessfulJob::class, $key);
        $this->assertJobWasReleasedImmediately(CircuitBreakerWithRedisTestJob::class, $key);
        $this->assertJobWasReleasedImmediately(CircuitBreakerWithRedisTestJob::class, $key);
        $this->assertJobWasReleasedWithDelay(CircuitBreakerWithRedisTestJob::class, $key);
    }

    protected function assertJobWasReleasedImmediately($class, $key)
    {
        $class::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->once()->andReturn(false);
        $job->shouldReceive('release')->with(0)->once();
        $job->shouldReceive('isReleased')->andReturn(true);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(true);

        $instance->call($job, [
            'command' => serialize($command = new $class($key)),
        ]);

        $this->assertTrue($class::$handled);
    }

    protected function assertJobWasReleasedWithDelay($class, $key)
    {
        $class::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->once()->andReturn(false);
        $job->shouldReceive('release')->withArgs(function ($delay) {
            return $delay >= 600;
        })->once();
        $job->shouldReceive('isReleased')->andReturn(true);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(true);

        $instance->call($job, [
            'command' => serialize($command = new $class($key)),
        ]);

        $this->assertFalse($class::$handled);
    }

    protected function assertJobRanSuccessfully($class, $key)
    {
        $class::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->once()->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(false);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(false);
        $job->shouldReceive('delete')->once();

        $instance->call($job, [
            'command' => serialize($command = new $class($key)),
        ]);

        $this->assertTrue($class::$handled);
    }
}

class CircuitBreakerWithRedisTestJob
{
    use InteractsWithQueue, Queueable;

    public static $handled = false;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function handle()
    {
        static::$handled = true;

        throw new Exception;
    }

    public function middleware()
    {
        return [(new ThrottlesExceptionsWithRedis(2, 10))->by($this->key)];
    }
}

class CircuitBreakerWithRedisSuccessfulJob
{
    use InteractsWithQueue, Queueable;

    public static $handled = false;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function handle()
    {
        static::$handled = true;
    }

    public function middleware()
    {
        return [(new ThrottlesExceptionsWithRedis(2, 10))->by($this->key)];
    }
}
