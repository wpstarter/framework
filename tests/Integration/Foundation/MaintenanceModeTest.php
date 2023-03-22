<?php

namespace WpStarter\Tests\Integration\Foundation;

use WpStarter\Foundation\Console\DownCommand;
use WpStarter\Foundation\Console\UpCommand;
use WpStarter\Foundation\Events\MaintenanceModeDisabled;
use WpStarter\Foundation\Events\MaintenanceModeEnabled;
use WpStarter\Foundation\Http\MaintenanceModeBypassCookie;
use WpStarter\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use WpStarter\Support\Carbon;
use WpStarter\Support\Facades\Event;
use WpStarter\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use Symfony\Component\HttpFoundation\Cookie;

class MaintenanceModeTest extends TestCase
{
    protected function tearDown(): void
    {
        @unlink(ws_storage_path('framework/down'));
    }

    public function testBasicMaintenanceModeResponse()
    {
        file_put_contents(ws_storage_path('framework/down'), json_encode([
            'retry' => 60,
            'refresh' => 60,
        ]));

        Route::get('/foo', function () {
            return 'Hello World';
        })->middleware(PreventRequestsDuringMaintenance::class);

        $response = $this->get('/foo');

        $response->assertStatus(503);
        $response->assertHeader('Retry-After', '60');
        $response->assertHeader('Refresh', '60');
    }

    public function testMaintenanceModeCanHaveCustomStatus()
    {
        file_put_contents(ws_storage_path('framework/down'), json_encode([
            'retry' => 60,
            'status' => 200,
        ]));

        Route::get('/foo', function () {
            return 'Hello World';
        })->middleware(PreventRequestsDuringMaintenance::class);

        $response = $this->get('/foo');

        $response->assertStatus(200);
        $response->assertHeader('Retry-After', '60');
    }

    public function testMaintenanceModeCanHaveCustomTemplate()
    {
        file_put_contents(ws_storage_path('framework/down'), json_encode([
            'retry' => 60,
            'template' => 'Rendered Content',
        ]));

        Route::get('/foo', function () {
            return 'Hello World';
        })->middleware(PreventRequestsDuringMaintenance::class);

        $response = $this->get('/foo');

        $response->assertStatus(503);
        $response->assertHeader('Retry-After', '60');
        $this->assertSame('Rendered Content', $response->original);
    }

    public function testMaintenanceModeCanRedirectWithBypassCookie()
    {
        file_put_contents(ws_storage_path('framework/down'), json_encode([
            'retry' => 60,
            'secret' => 'foo',
            'template' => 'Rendered Content',
        ]));

        Route::get('/foo', function () {
            return 'Hello World';
        })->middleware(PreventRequestsDuringMaintenance::class);

        $response = $this->get('/foo');

        $response->assertStatus(302);
        $response->assertCookie('laravel_maintenance');
    }

    public function testMaintenanceModeCanBeBypassedWithValidCookie()
    {
        file_put_contents(ws_storage_path('framework/down'), json_encode([
            'retry' => 60,
            'secret' => 'foo',
        ]));

        $cookie = MaintenanceModeBypassCookie::create('foo');

        Route::get('/test', function () {
            return 'Hello World';
        })->middleware(PreventRequestsDuringMaintenance::class);

        $response = $this->withUnencryptedCookies([
            'laravel_maintenance' => $cookie->getValue(),
        ])->get('/test');

        $response->assertStatus(200);
        $this->assertSame('Hello World', $response->original);
    }

    public function testMaintenanceModeCantBeBypassedWithInvalidCookie()
    {
        file_put_contents(ws_storage_path('framework/down'), json_encode([
            'retry' => 60,
            'secret' => 'foo',
        ]));

        $cookie = MaintenanceModeBypassCookie::create('test-key');

        Route::get('/test', function () {
            return 'Hello World';
        })->middleware(PreventRequestsDuringMaintenance::class);

        $response = $this->withUnencryptedCookies([
            'laravel_maintenance' => $cookie->getValue(),
        ])->get('/test');

        $response->assertStatus(503);
    }

    public function testCanCreateBypassCookies()
    {
        $cookie = MaintenanceModeBypassCookie::create('test-key');

        $this->assertInstanceOf(Cookie::class, $cookie);
        $this->assertSame('laravel_maintenance', $cookie->getName());

        $this->assertTrue(MaintenanceModeBypassCookie::isValid($cookie->getValue(), 'test-key'));
        $this->assertFalse(MaintenanceModeBypassCookie::isValid($cookie->getValue(), 'wrong-key'));

        Carbon::setTestNow(ws_now()->addMonths(6));
        $this->assertFalse(MaintenanceModeBypassCookie::isValid($cookie->getValue(), 'test-key'));

        Carbon::setTestNow(null);
    }

    public function testDispatchEventWhenMaintenanceModeIsEnabled()
    {
        Event::fake();

        Event::assertNotDispatched(MaintenanceModeEnabled::class);
        $this->artisan(DownCommand::class);
        Event::assertDispatched(MaintenanceModeEnabled::class);
    }

    public function testDispatchEventWhenMaintenanceModeIsDisabled()
    {
        file_put_contents(ws_storage_path('framework/down'), json_encode([
            'retry' => 60,
            'refresh' => 60,
        ]));

        Event::fake();

        Event::assertNotDispatched(MaintenanceModeDisabled::class);
        $this->artisan(UpCommand::class);
        Event::assertDispatched(MaintenanceModeDisabled::class);
    }
}