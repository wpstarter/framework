<?php

namespace WpStarter\Tests\Console;

use WpStarter\Console\Signals;
use WpStarter\Tests\Console\Fixtures\FakeSignalsRegistry;
use PHPUnit\Framework\TestCase;

class SignalsTest extends TestCase
{
    protected $registry;

    protected $signals;

    protected $state;

    protected function setUp(): void
    {
        $this->registry = new FakeSignalsRegistry();
        $this->signals = new Signals($this->registry);
    }

    protected function tearDown(): void
    {
        $this->state = null;

        parent::tearDown();
    }

    public function testRegister()
    {
        $this->signals->register('my-signal', function () {
            $this->state .= 'otwell';
        });

        $this->signals->register('my-signal', function () {
            $this->state = 'taylor';
        });

        $this->registry->handle('my-signal');

        $this->assertSame('taylorotwell', $this->state);
    }

    public function testUnregister()
    {
        $this->signals->register('my-signal', function () {
            $this->state .= 'otwell';
        });

        $this->signals->register('my-signal', function () {
            $this->state = 'taylor';
        });

        $this->signals->unregister();

        $this->registry->handle('my-signal');

        $this->assertNull($this->state);
    }
}
