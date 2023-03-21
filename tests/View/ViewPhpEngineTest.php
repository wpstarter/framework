<?php

namespace WpStarter\Tests\View;

use WpStarter\Filesystem\Filesystem;
use WpStarter\View\Engines\PhpEngine;
use PHPUnit\Framework\TestCase;

class ViewPhpEngineTest extends TestCase
{
    public function testViewsMayBeProperlyRendered()
    {
        $engine = new PhpEngine(new Filesystem);
        $this->assertSame('Hello World
', $engine->get(__DIR__.'/fixtures/basic.php'));
    }
}
