<?php

namespace WpStarter\Tests\Integration\Routing;

use WpStarter\Support\Facades\Route;
use Orchestra\Testbench\TestCase;

class SimpleRouteTest extends TestCase
{
    public function testSimpleRouteThroughTheFramework()
    {
        Route::get('/', function () {
            return 'Hello World';
        });

        $response = $this->get('/');

        $this->assertSame('Hello World', $response->content());
    }
}
