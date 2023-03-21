<?php

namespace WpStarter\Tests\Integration\Queue;

use WpStarter\Bus\Dispatcher;
use WpStarter\Bus\Queueable;
use WpStarter\Cache\RateLimiter;
use WpStarter\Cache\RateLimiting\Limit;
use WpStarter\Contracts\Queue\Job;
use WpStarter\Contracts\Redis\Factory as Redis;
use WpStarter\Foundation\Testing\Concerns\InteractsWithRedis;
use WpStarter\Queue\CallQueuedHandler;
use WpStarter\Queue\InteractsWithQueue;
use WpStarter\Queue\Middleware\RateLimitedWithRedis;
use WpStarter\Support\Str;
use Mockery as m;
use Orchestra\Testbench\TestCase;

class RateLimitedWithRedisTest extends TestCase
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

    public function testUnlimitedJobsAreExecuted()
    {
        $rateLimiter = $this->app->make(RateLimiter::class);

        $testJob = new RedisRateLimitedTestJob;

        $rateLimiter->for($testJob->key, function ($job) {
            return Limit::none();
        });

        $this->assertJobRanSuccessfully($testJob);
        $this->assertJobRanSuccessfully($testJob);
    }

    public function testRateLimitedJobsAreNotExecutedOnLimitReached()
    {
        $rateLimiter = $this->app->make(RateLimiter::class);

        $testJob = new RedisRateLimitedTestJob;

        $rateLimiter->for($testJob->key, function ($job) {
            return Limit::perMinute(1);
        });

        $this->assertJobRanSuccessfully($testJob);
        $this->assertJobWasReleased($testJob);
    }

    public function testRateLimitedJobsCanBeSkippedOnLimitReached()
    {
        $rateLimiter = $this->app->make(RateLimiter::class);

        $testJob = new RedisRateLimitedDontReleaseTestJob;

        $rateLimiter->for($testJob->key, function ($job) {
            return Limit::perMinute(1);
        });

        $this->assertJobRanSuccessfully($testJob);
        $this->assertJobWasSkipped($testJob);
    }

    public function testJobsCanHaveConditionalRateLimits()
    {
        $rateLimiter = $this->app->make(RateLimiter::class);

        $adminJob = new RedisAdminTestJob;

        $rateLimiter->for($adminJob->key, function ($job) {
            if ($job->isAdmin()) {
                return Limit::none();
            }

            return Limit::perMinute(1);
        });

        $this->assertJobRanSuccessfully($adminJob);
        $this->assertJobRanSuccessfully($adminJob);

        $nonAdminJob = new RedisNonAdminTestJob;

        $rateLimiter->for($nonAdminJob->key, function ($job) {
            if ($job->isAdmin()) {
                return Limit::none();
            }

            return Limit::perMinute(1);
        });

        $this->assertJobRanSuccessfully($nonAdminJob);
        $this->assertJobWasReleased($nonAdminJob);
    }

    public function testMiddlewareSerialization()
    {
        $rateLimited = new RateLimitedWithRedis('limiterName');
        $rateLimited->shouldRelease = false;

        $restoredRateLimited = unserialize(serialize($rateLimited));

        $fetch = (function (string $name) {
            return $this->{$name};
        })->bindTo($restoredRateLimited, RateLimitedWithRedis::class);

        $this->assertFalse($restoredRateLimited->shouldRelease);
        $this->assertSame('limiterName', $fetch('limiterName'));
        $this->assertInstanceOf(RateLimiter::class, $fetch('limiter'));
        $this->assertInstanceOf(Redis::class, $fetch('redis'));
    }

    protected function assertJobRanSuccessfully($testJob)
    {
        $testJob::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->once()->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(false);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(false);
        $job->shouldReceive('delete')->once();

        $instance->call($job, [
            'command' => serialize($testJob),
        ]);

        $this->assertTrue($testJob::$handled);
    }

    protected function assertJobWasReleased($testJob)
    {
        $testJob::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->once()->andReturn(false);
        $job->shouldReceive('release')->once();
        $job->shouldReceive('isReleased')->andReturn(true);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(true);

        $instance->call($job, [
            'command' => serialize($testJob),
        ]);

        $this->assertFalse($testJob::$handled);
    }

    protected function assertJobWasSkipped($testJob)
    {
        $testJob::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->once()->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(false);
        $job->shouldReceive('isDeletedOrReleased')->once()->andReturn(false);
        $job->shouldReceive('delete')->once();

        $instance->call($job, [
            'command' => serialize($testJob),
        ]);

        $this->assertFalse($testJob::$handled);
    }
}

class RedisRateLimitedTestJob
{
    use InteractsWithQueue, Queueable;

    public $key;

    public static $handled = false;

    public function __construct()
    {
        $this->key = Str::random(10);
    }

    public function handle()
    {
        static::$handled = true;
    }

    public function middleware()
    {
        return [new RateLimitedWithRedis($this->key)];
    }
}

class RedisAdminTestJob extends RedisRateLimitedTestJob
{
    public function isAdmin()
    {
        return true;
    }
}

class RedisNonAdminTestJob extends RedisRateLimitedTestJob
{
    public function isAdmin()
    {
        return false;
    }
}

class RedisRateLimitedDontReleaseTestJob extends RedisRateLimitedTestJob
{
    public function middleware()
    {
        return [(new RateLimitedWithRedis($this->key))->dontRelease()];
    }
}
