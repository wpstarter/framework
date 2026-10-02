<?php

namespace WpStarter\Tests\View\Blade;

use WpStarter\Container\Container;
use WpStarter\Filesystem\Filesystem;
use WpStarter\View\Compilers\BladeCompiler;
use WpStarter\View\Component;
use Mockery as m;
use PHPUnit\Framework\TestCase;

abstract class AbstractBladeTestCase extends TestCase
{
    /**
     * @var \WpStarter\View\Compilers\BladeCompiler
     */
    protected $compiler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->compiler = new BladeCompiler($this->getFiles(), __DIR__);
    }

    protected function tearDown(): void
    {
        Container::setInstance(null);
        Component::flushCache();
        Component::forgetComponentsResolver();
        Component::forgetFactory();

        parent::tearDown();
    }

    protected function getFiles()
    {
        return m::mock(Filesystem::class);
    }
}
