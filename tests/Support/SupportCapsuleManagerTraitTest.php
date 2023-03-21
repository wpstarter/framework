<?php

namespace WpStarter\Tests\Support;

use WpStarter\Config\Repository;
use WpStarter\Container\Container;
use WpStarter\Support\Fluent;
use WpStarter\Support\Traits\CapsuleManagerTrait;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class SupportCapsuleManagerTraitTest extends TestCase
{
    use CapsuleManagerTrait;

    protected function tearDown(): void
    {
        m::close();
    }

    public function testSetupContainerForCapsule()
    {
        $this->container = null;
        $app = new Container;

        $this->setupContainer($app);
        $this->assertEquals($app, $this->getContainer());
        $this->assertInstanceOf(Fluent::class, $app['config']);
    }

    public function testSetupContainerForCapsuleWhenConfigIsBound()
    {
        $this->container = null;
        $app = new Container;
        $app['config'] = m::mock(Repository::class);

        $this->setupContainer($app);
        $this->assertEquals($app, $this->getContainer());
        $this->assertInstanceOf(Repository::class, $app['config']);
    }
}
