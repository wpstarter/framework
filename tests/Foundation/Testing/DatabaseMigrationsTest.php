<?php

namespace WpStarter\Tests\Foundation\Testing;

use WpStarter\Contracts\Console\Kernel as ConsoleKernelContract;
use WpStarter\Foundation\Console\Kernel as ConsoleKernel;
use WpStarter\Foundation\Testing\Concerns\InteractsWithConsole;
use WpStarter\Foundation\Testing\DatabaseMigrations;
use WpStarter\Foundation\Testing\RefreshDatabaseState;
use Mockery as m;
use Orchestra\Testbench\Concerns\ApplicationTestingHooks;
use Orchestra\Testbench\Foundation\Application as Testbench;
use PHPUnit\Framework\TestCase;

use function Orchestra\Testbench\package_path;

class DatabaseMigrationsTest extends TestCase
{
    use ApplicationTestingHooks;
    use DatabaseMigrations;
    use InteractsWithConsole;

    public $dropViews = false;

    public $dropTypes = false;

    protected function setUp(): void
    {
        RefreshDatabaseState::$migrated = false;

        $this->afterApplicationCreated(function () {
            $this->app['config']->set([
                'database.default' => 'testing',
                'database.connections.testing' => [
                    'driver' => 'sqlite',
                    'database' => ':memory:',
                ],
            ]);
        });

        $this->setUpTheApplicationTestingHooks();
        $this->withoutMockingConsoleOutput();
    }

    protected function tearDown(): void
    {
        $this->tearDownTheApplicationTestingHooks();

        RefreshDatabaseState::$migrated = false;

        parent::tearDown();
    }

    protected function refreshApplication()
    {
        $this->app = Testbench::create(
            basePath: package_path('vendor/orchestra/testbench-core/laravel'),
        );
    }

    public function testRefreshTestDatabaseDefault()
    {
        $this->app->instance(ConsoleKernelContract::class, $kernel = m::spy(ConsoleKernel::class));

        $kernel->shouldReceive('call')
            ->once()
            ->with('migrate:fresh', [
                '--drop-views' => false,
                '--drop-types' => false,
                '--seed' => false,
            ]);

        $this->runDatabaseMigrations();
    }

    public function testRefreshTestDatabaseWithDropViewsOption()
    {
        $this->dropViews = true;

        $this->app->instance(ConsoleKernelContract::class, $kernel = m::spy(ConsoleKernel::class));

        $kernel->shouldReceive('call')
            ->once()
            ->with('migrate:fresh', [
                '--drop-views' => true,
                '--drop-types' => false,
                '--seed' => false,
            ]);

        $this->runDatabaseMigrations();
    }

    public function testRefreshTestDatabaseWithDropTypesOption()
    {
        $this->dropTypes = true;

        $this->app->instance(ConsoleKernelContract::class, $kernel = m::spy(ConsoleKernel::class));

        $kernel->shouldReceive('call')
            ->once()
            ->with('migrate:fresh', [
                '--drop-views' => false,
                '--drop-types' => true,
                '--seed' => false,
            ]);

        $this->runDatabaseMigrations();
    }
}
