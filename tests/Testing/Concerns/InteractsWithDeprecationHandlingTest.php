<?php

namespace WpStarter\Tests\Testing\Concerns;

use ErrorException;
use WpStarter\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use PHPUnit\Framework\TestCase;

class InteractsWithDeprecationHandlingTest extends TestCase
{
    use InteractsWithDeprecationHandling;

    protected $original;

    protected $deprecationsFound = false;

    public function setUp(): void
    {
        parent::setUp();

        $this->original = set_error_handler(function () {
            $this->deprecationsFound = true;
        });
    }

    public function testWithDeprecationHandling()
    {
        $this->withDeprecationHandling();

        trigger_error('Something is deprecated', E_USER_DEPRECATED);

        $this->assertTrue($this->deprecationsFound);
    }

    public function testWithoutDeprecationHandling()
    {
        $this->withoutDeprecationHandling();

        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('Something is deprecated');

        trigger_error('Something is deprecated', E_USER_DEPRECATED);
    }

    public function tearDown(): void
    {
        set_error_handler($this->original);

        $this->originalDeprecationHandler = null;
        $this->deprecationsFound = false;

        parent::tearDown();
    }
}
