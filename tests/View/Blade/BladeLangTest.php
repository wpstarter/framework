<?php

namespace WpStarter\Tests\View\Blade;

class BladeLangTest extends AbstractBladeTestCase
{
    public function testStatementThatContainsNonConsecutiveParenthesisAreCompiled()
    {
        $string = "Foo @lang(function_call('foo(blah)')) bar";
        $expected = "Foo <?php echo app('translator')->get(function_call('foo(blah)')); ?> bar";
        $this->assertEquals($expected, $this->compiler->compileString($string));
    }

    public function testLanguageAndChoicesAreCompiled()
    {
        $this->assertSame('); ?>', $this->compiler->compileString("@lang('foo')"));
        $this->assertSame(', 1); ?>', $this->compiler->compileString("@choice('foo', 1)"));
    }
}
