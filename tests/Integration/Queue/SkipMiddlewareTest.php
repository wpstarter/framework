<?php

namespace WpStarter\Tests\Integration\Queue;

use WpStarter\Bus\Dispatcher;
use WpStarter\Bus\Queueable;
use WpStarter\Contracts\Queue\Job;
use WpStarter\Queue\CallQueuedHandler;
use WpStarter\Queue\InteractsWithQueue;
use WpStarter\Queue\Middleware\Skip;
use Laravel\SerializableClosure\SerializableClosure;
use Mockery as m;
use Orchestra\Testbench\TestCase;

class SkipMiddlewareTest extends TestCase
{
    public function testJobIsSkippedWhenConditionIsTrue()
    {
        $job = new SkipTestJob(skip: true);

        $this->assertJobWasSkipped($job);
    }

    public function testJobIsSkippedWhenConditionIsTrueUsingClosure()
    {
        $job = new SkipTestJob(skip: new SerializableClosure(fn () => true));

        $this->assertJobWasSkipped($job);
    }

    public function testJobIsNotSkippedWhenConditionIsFalse()
    {
        $job = new SkipTestJob(skip: false);

        $this->assertJobRanSuccessfully($job);
    }

    public function testJobIsNotSkippedWhenConditionIsFalseUsingClosure()
    {
        $job = new SkipTestJob(skip: new SerializableClosure(fn () => false));

        $this->assertJobRanSuccessfully($job);
    }

    public function testJobIsNotSkippedWhenConditionIsTrueWithUnless()
    {
        $job = new SkipTestJob(skip: true, useUnless: true);

        $this->assertJobRanSuccessfully($job);
    }

    public function testJobIsNotSkippedWhenConditionIsTrueWithUnlessUsingClosure()
    {
        $job = new SkipTestJob(skip: new SerializableClosure(fn () => true), useUnless: true);

        $this->assertJobRanSuccessfully($job);
    }

    public function testJobIsSkippedWhenConditionIsFalseWithUnless()
    {
        $job = new SkipTestJob(skip: false, useUnless: true);

        $this->assertJobWasSkipped($job);
    }

    public function testJobIsSkippedWhenConditionIsFalseWithUnlessUsingClosure()
    {
        $job = new SkipTestJob(skip: new SerializableClosure(fn () => false), useUnless: true);

        $this->assertJobWasSkipped($job);
    }

    protected function assertJobRanSuccessfully(SkipTestJob $class)
    {
        $this->assertJobHandled(class: $class, expectedHandledValue: true);
    }

    protected function assertJobWasSkipped(SkipTestJob $class)
    {
        $this->assertJobHandled(class: $class, expectedHandledValue: false);
    }

    protected function assertJobHandled(SkipTestJob $class, bool $expectedHandledValue)
    {
        $class::$handled = false;
        $instance = new CallQueuedHandler(new Dispatcher($this->app), $this->app);

        $job = m::mock(Job::class);

        $job->shouldReceive('hasFailed')->andReturn(false);
        $job->shouldReceive('isReleased')->andReturn(false);
        $job->shouldReceive('isDeletedOrReleased')->andReturn(false);
        $job->shouldReceive('delete')->once();

        $instance->call($job, [
            'command' => serialize($class),
        ]);

        $this->assertEquals($expectedHandledValue, $class::$handled);
    }
}

class SkipTestJob
{
    use InteractsWithQueue, Queueable;

    public static $handled = false;

    public function __construct(
        protected bool|SerializableClosure $skip,
        protected bool $useUnless = false,
    ) {
    }

    public function handle(): void
    {
        static::$handled = true;
    }

    public function middleware(): array
    {
        $skip = $this->skip instanceof SerializableClosure
            ? $this->skip->getClosure()
            : $this->skip;

        if ($this->useUnless) {
            return [Skip::unless($skip)];
        }

        return [Skip::when($skip)];
    }
}
