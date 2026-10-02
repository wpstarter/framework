<?php

namespace WpStarter\Tests\View\Blade;

class BladeIfAuthStatementsTest extends AbstractBladeTestCase
{
    public function testIfStatementsAreCompiled()
    {
        $string = '@auth("api")
breeze
@endauth';
        $expected = '<?php if(ws_auth()->guard("api")->check()): ?>
breeze
<?php endif; ?>';
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }

    public function testPlainIfStatementsAreCompiled()
    {
        $string = '@auth
breeze
@endauth';
        $expected = '<?php if(ws_auth()->guard()->check()): ?>
breeze
<?php endif; ?>';
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }
}
