<?php

namespace WpStarter\Tests\Integration\Support;

use WpStarter\Support\Facades\Auth;
use Orchestra\Testbench\TestCase;
use RuntimeException;

class AuthFacadeTest extends TestCase
{
    public function testItFailsIfTheUiPackageIsMissing()
    {
        $this->expectExceptionObject(new RuntimeException(
            'In order to use the Auth::routes() method, please install the laravel/ui package.'
        ));

        Auth::routes();
    }
}
