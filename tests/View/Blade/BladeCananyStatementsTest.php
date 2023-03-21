<?php

namespace WpStarter\Tests\View\Blade;

class BladeCananyStatementsTest extends AbstractBladeTestCase
{
    public function testCananyStatementsAreCompiled()
    {
        $string = '@canany ([\'create\', \'update\'], [$post])
breeze
@elsecanany([\'delete\', \'approve\'], [$post])
sneeze
@endcan';
        $expected = '<?php if (app(\\WpStarter\\Contracts\\Auth\\Access\\Gate::class)->any([\'create\', \'update\'], [$post])): ?>
breeze
<?php elseif (app(\\WpStarter\\Contracts\\Auth\\Access\\Gate::class)->any([\'delete\', \'approve\'], [$post])): ?>
sneeze
<?php endif; ?>';
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }
}
