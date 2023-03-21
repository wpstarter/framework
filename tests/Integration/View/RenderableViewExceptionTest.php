<?php

namespace WpStarter\Tests\Integration\View;

use Exception;
use WpStarter\Http\Response;
use WpStarter\Support\Facades\Route;
use WpStarter\Support\Facades\View;
use Orchestra\Testbench\TestCase;

class RenderableViewExceptionTest extends TestCase
{
    public function testRenderMethodOfExceptionThrownInViewGetsHandled()
    {
        Route::get('/', function () {
            return View::make('renderable-exception');
        });

        $response = $this->get('/');

        $response->assertSee('This is a renderable exception.');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('view.paths', [__DIR__.'/templates']);
    }
}

class RenderableException extends Exception
{
    public function render($request)
    {
        return new Response('This is a renderable exception.');
    }
}
