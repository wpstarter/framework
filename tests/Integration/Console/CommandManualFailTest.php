<?php

namespace WpStarter\Tests\Integration\Console;

use WpStarter\Console\Application as Artisan;
use WpStarter\Console\Command;
use WpStarter\Console\ManuallyFailedException;
use Orchestra\Testbench\TestCase;

class CommandManualFailTest extends TestCase
{
    protected function setUp(): void
    {
        Artisan::starting(function ($artisan) {
            $artisan->resolveCommands([
                FailingCommandStub::class,
            ]);
        });

        parent::setUp();
    }

    public function testFailArtisanCommandManually(): void
    {
        $this->artisan('app:fail')->assertFailed();
    }

    public function testCreatesAnExceptionFromString(): void
    {
        $this->expectException(ManuallyFailedException::class);
        $this->expectExceptionMessage('Whoops!');
        $command = new Command;
        $command->fail('Whoops!');
    }

    public function testCreatesAnExceptionFromNull(): void
    {
        $this->expectException(ManuallyFailedException::class);
        $this->expectExceptionMessage('Command failed manually.');
        $command = new Command;
        $command->fail();
    }

    public function testThrowsTheOriginalThrowableInstance(): void
    {
        try {
            $command = new Command;
            $command->fail($original = new \RuntimeException('Something went wrong.'));

            $this->fail('Command::fail() method must throw the original throwable instance.');
        } catch (\Throwable $e) {
            $this->assertSame($original, $e);
        }
    }
}

class FailingCommandStub extends Command
{
    protected $signature = 'app:fail';

    public function handle()
    {
        $this->trigger_failure();

        // This should never be reached.
        return static::SUCCESS;
    }

    protected function trigger_failure()
    {
        $this->fail('Whoops!');
    }
}
