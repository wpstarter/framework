<?php

namespace WpStarter\Tests\View\Blade;

class BladeCanStatementsTest extends AbstractBladeTestCase
{
    public function testCanStatementsAreCompiled()
    {
        $string = '@can (\'update\', [$post])
breeze
@elsecan(\'delete\', [$post])
sneeze
@endcan';
        $expected = ', [$post])): ?>
sneeze
<?php endif; ?>\'';
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }
}
