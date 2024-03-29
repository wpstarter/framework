<?php

namespace WpStarter\Tests\Integration\Foundation;

use Exception;
use WpStarter\Contracts\Debug\ExceptionHandler;
use WpStarter\Support\Facades\Route;
use WpStarter\Support\Str;
use Orchestra\Testbench\TestCase;

class FoundationHelpersTest extends TestCase
{
    public function testRescue()
    {
        $this->assertEquals(
            'rescued!',
            ws_rescue(function () {
                throw new Exception;
            }, 'rescued!')
        );

        $this->assertEquals(
            'rescued!',
            ws_rescue(function () {
                throw new Exception;
            }, function () {
                return 'rescued!';
            })
        );

        $this->assertEquals(
            'no need to rescue',
            ws_rescue(function () {
                return 'no need to rescue';
            }, 'rescued!')
        );

        $testClass = new class
        {
            public function test(int $a)
            {
                return $a;
            }
        };

        $this->assertEquals(
            'rescued!',
            ws_rescue(function () use ($testClass) {
                $testClass->test([]);
            }, 'rescued!')
        );
    }

    public function testMixReportsExceptionWhenAssetIsMissingFromManifest()
    {
        $handler = new FakeHandler;
        $this->app->instance(ExceptionHandler::class, $handler);
        $manifest = $this->makeManifest();

        ws_mix('missing.js');

        $this->assertInstanceOf(Exception::class, $handler->reported[0]);
        $this->assertSame('Unable to locate Mix file: /missing.js.', $handler->reported[0]->getMessage());

        unlink($manifest);
    }

    public function testMixSilentlyFailsWhenAssetIsMissingFromManifestWhenNotInDebugMode()
    {
        $this->app['config']->set('app.debug', false);

        $manifest = $this->makeManifest();

        $path = ws_mix('missing.js');

        $this->assertSame('/missing.js', $path);

        unlink($manifest);
    }

    public function testMixThrowsExceptionWhenAssetIsMissingFromManifestWhenInDebugMode()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to locate Mix file: /missing.js.');

        $this->app['config']->set('app.debug', true);

        $manifest = $this->makeManifest();

        try {
            ws_mix('missing.js');
        } catch (Exception $e) {
            throw $e;
        } finally { // make sure we can cleanup the file
            unlink($manifest);
        }
    }

    public function testMixOnlyThrowsAndReportsOneExceptionWhenAssetIsMissingFromManifestWhenInDebugMode()
    {
        $handler = new FakeHandler;
        $this->app->instance(ExceptionHandler::class, $handler);
        $this->app['config']->set('app.debug', true);

        $manifest = $this->makeManifest();

        Route::get('test-route', function () {
            ws_mix('missing.js');
        });

        $this->get('/test-route');

        $this->assertCount(1, $handler->reported);

        unlink($manifest);
    }

    protected function makeManifest($directory = '')
    {
        $this->app->singleton('path.public', function () {
            return __DIR__;
        });

        $path = ws_public_path(Str::finish($directory, '/').'mix-manifest.json');

        touch($path);

        // Laravel mix prints JSON pretty and with escaped
        // slashes, so we are doing that here for consistency.
        $content = json_encode(['/unversioned.css' => '/versioned.css'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        file_put_contents($path, $content);

        return $path;
    }
}

class FakeHandler
{
    public $reported = [];

    public function report($exception)
    {
        $this->reported[] = $exception;
    }

    public function render($exception)
    {
        //
    }
}
