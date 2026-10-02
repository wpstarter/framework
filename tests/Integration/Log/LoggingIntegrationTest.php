<?php

namespace WpStarter\Tests\Integration\Log;

use WpStarter\Log\Events\MessageLogged;
use WpStarter\Log\Logger;
use WpStarter\Support\Facades\Event;
use WpStarter\Support\Facades\Log;
use Orchestra\Testbench\TestCase;

class LoggingIntegrationTest extends TestCase
{
    public function testLoggingCanBeRunWithoutEncounteringExceptions()
    {
        $this->expectNotToPerformAssertions();

        Log::info('Hello World');
    }

    public function testCallingLoggerDirectlyDispatchesOneEvent()
    {
        Event::fake([MessageLogged::class]);

        $this->app->make(Logger::class)->debug('my debug message');

        Event::assertDispatchedTimes(MessageLogged::class, 1);
    }
}
