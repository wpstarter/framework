<?php

namespace WpStarter\Tests\Integration\Cache;

use Exception;
use WpStarter\Support\Carbon;
use WpStarter\Support\Facades\Cache;
use Orchestra\Testbench\TestCase;

class FileCacheLockTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \WpStarter\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.default', 'file');
    }

    public function testLocksCanBeAcquiredAndReleased()
    {
        Cache::lock('foo')->forceRelease();

        $lock = Cache::lock('foo', 10);
        $this->assertTrue($lock->get());
        $this->assertFalse(Cache::lock('foo', 10)->get());
        $lock->release();

        $lock = Cache::lock('foo', 10);
        $this->assertTrue($lock->get());
        $this->assertFalse(Cache::lock('foo', 10)->get());
        Cache::lock('foo')->release();
    }

    public function testLocksCanBlockForSeconds()
    {
        Carbon::setTestNow();

        Cache::lock('foo')->forceRelease();
        $this->assertSame('taylor', Cache::lock('foo', 10)->block(1, function () {
            return 'taylor';
        }));

        Cache::lock('foo')->forceRelease();
        $this->assertTrue(Cache::lock('foo', 10)->block(1));
    }

    public function testConcurrentLocksAreReleasedSafely()
    {
        Cache::lock('foo')->forceRelease();

        $firstLock = Cache::lock('foo', 1);
        $this->assertTrue($firstLock->get());
        sleep(2);

        $secondLock = Cache::lock('foo', 10);
        $this->assertTrue($secondLock->get());

        $firstLock->release();

        $this->assertFalse(Cache::lock('foo')->get());
    }

    public function testLocksWithFailedBlockCallbackAreReleased()
    {
        Cache::lock('foo')->forceRelease();

        $firstLock = Cache::lock('foo', 10);

        try {
            $firstLock->block(1, function () {
                throw new Exception('failed');
            });
        } catch (Exception $e) {
            // Not testing the exception, just testing the lock
            // is released regardless of the how the exception
            // thrown by the callback was handled.
        }

        $secondLock = Cache::lock('foo', 1);

        $this->assertTrue($secondLock->get());
    }

    public function testLocksCanBeReleasedUsingOwnerToken()
    {
        Cache::lock('foo')->forceRelease();

        $firstLock = Cache::lock('foo', 10);
        $this->assertTrue($firstLock->get());
        $owner = $firstLock->owner();

        $secondLock = Cache::store('file')->restoreLock('foo', $owner);
        $secondLock->release();

        $this->assertTrue(Cache::lock('foo')->get());
    }
}
