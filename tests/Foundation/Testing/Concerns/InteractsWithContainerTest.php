<?php

namespace WpStarter\Tests\Foundation\Bootstrap\Testing\Concerns;

use WpStarter\Foundation\Mix;
use Orchestra\Testbench\TestCase;
use stdClass;

class InteractsWithContainerTest extends TestCase
{
    public function testWithoutMixBindsEmptyHandlerAndReturnsInstance()
    {
        $instance = $this->withoutMix();

        $this->assertSame('', ws_mix('path/to/asset.png'));
        $this->assertSame($this, $instance);
    }

    public function testWithMixRestoresOriginalHandlerAndReturnsInstance()
    {
        $handler = new stdClass;
        $this->app->instance(Mix::class, $handler);

        $this->withoutMix();
        $instance = $this->withMix();

        $this->assertSame($handler, ws_resolve(Mix::class));
        $this->assertSame($this, $instance);
    }

    public function testForgetMock()
    {
        $this->mock(IntanceStub::class)
            ->shouldReceive('execute')
            ->once()
            ->andReturn('bar');

        $this->assertSame('bar', $this->app->make(IntanceStub::class)->execute());

        $this->forgetMock(IntanceStub::class);
        $this->assertSame('foo', $this->app->make(IntanceStub::class)->execute());
    }
}

class IntanceStub
{
    public function execute()
    {
        return 'foo';
    }
}
