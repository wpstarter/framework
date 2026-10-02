<?php

namespace WpStarter\Tests\Queue;

use WpStarter\Queue\Jobs\RedisJob;
use WpStarter\Queue\MaxAttemptsExceededException;
use WpStarter\Queue\TimeoutExceededException;
use PHPUnit\Framework\TestCase;

class QueueExceptionTest extends TestCase
{
    public function test_it_can_create_timeout_exception_for_job()
    {
        $e = TimeoutExceededException::forJob($job = new MyFakeRedisJob());

        $this->assertSame('App\\Jobs\\UnderlyingJob has timed out.', $e->getMessage());
        $this->assertSame($job, $e->job);
    }

    public function test_it_can_create_max_attempts_exception_for_job()
    {
        $e = MaxAttemptsExceededException::forJob($job = new MyFakeRedisJob());

        $this->assertSame('App\\Jobs\\UnderlyingJob has been attempted too many times.', $e->getMessage());
        $this->assertSame($job, $e->job);
    }
}

class MyFakeRedisJob extends RedisJob
{
    public function __construct()
    {
        //
    }

    public function resolveName()
    {
        return 'App\\Jobs\\UnderlyingJob';
    }
}
