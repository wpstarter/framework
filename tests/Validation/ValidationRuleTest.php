<?php

namespace WpStarter\Tests\Validation;

use WpStarter\Validation\Rule;
use PHPUnit\Framework\TestCase;

class ValidationRuleTest extends TestCase
{
    public function testMacroable()
    {
        // phone macro : validate a phone number
        Rule::macro('phone', function () {
            return 'regex:/^([0-9\s\-\+\(\)]*)$/';
        });
        $c = Rule::phone();
        $this->assertSame('regex:/^([0-9\s\-\+\(\)]*)$/', $c);
    }
}
