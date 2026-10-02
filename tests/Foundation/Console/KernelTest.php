<?php

namespace WpStarter\Tests\Foundation\Console;

use WpStarter\Events\Dispatcher;
use WpStarter\Foundation\Application;
use WpStarter\Foundation\Console\Kernel;
use WpStarter\Foundation\Events\Terminating;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;

class KernelTest extends TestCase
{
    public function testItDispatchesTerminatingEvent()
    {
        $called = [];
        $app = new Application;
        $events = new Dispatcher($app);
        $app->instance('events', $events);
        $kernel = new Kernel($app, $events);
        $events->listen(function (Terminating $terminating) use (&$called) {
            $called[] = 'terminating event';
        });
        $app->terminating(function () use (&$called) {
            $called[] = 'terminating callback';
        });

        $kernel->terminate(new StringInput('tinker'), 0);

        $this->assertSame([
            'terminating event',
            'terminating callback',
        ], $called);
    }
}
