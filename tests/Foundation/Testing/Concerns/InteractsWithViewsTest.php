<?php

namespace WpStarter\Tests\Foundation\Bootstrap\Testing\Concerns;

use WpStarter\Foundation\Testing\Concerns\InteractsWithViews;
use WpStarter\View\Component;
use Orchestra\Testbench\TestCase;

class InteractsWithViewsTest extends TestCase
{
    use InteractsWithViews;

    public function testBladeCorrectlyRendersString()
    {
        $string = (string) $this->blade('@if(true)test @endif');

        $this->assertEquals('test ', $string);
    }

    public function testComponentCanAccessPublicProperties()
    {
        $exampleComponent = new class extends Component
        {
            public $foo = 'bar';

            public function speak()
            {
                return 'hello';
            }

            public function render()
            {
                return 'rendered content';
            }
        };

        $component = $this->component(get_class($exampleComponent));

        $this->assertEquals('bar', $component->foo);
        $this->assertEquals('hello', $component->speak());
        $component->assertSee('content');
    }
}
