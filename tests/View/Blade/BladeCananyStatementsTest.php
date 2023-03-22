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
        $expected = '], [$post])): ?>
sneeze
<?php endif; ?>\'';
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }
}
