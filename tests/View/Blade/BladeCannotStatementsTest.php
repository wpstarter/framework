<?php

namespace WpStarter\Tests\View\Blade;

class BladeCannotStatementsTest extends AbstractBladeTestCase
{
    public function testCannotStatementsAreCompiled()
    {
        $string = '@cannot (\'update\', [$post])
breeze
@elsecannot(\'delete\', [$post])
sneeze
@endcannot';
        $expected = ', [$post])): ?>
sneeze
<?php endif; ?>\'';
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }
}
