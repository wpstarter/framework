<?php

namespace WpStarter\Tests\Redis;

use Exception;
use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Redis\Connections\PhpRedisConnection;
use WpStarter\Redis\Events\CommandExecuted;
use WpStarter\Redis\Events\CommandFailed;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Redis;

class RedisEventsTest extends TestCase
{
    public function testCommandFailedEventIsDispatched()
    {
        $exception = new Exception('Test exception');

        $client = m::mock(Redis::class);
        $client->shouldReceive('get')->with('key')->andThrow($exception);

        $events = m::mock(Dispatcher::class);
        $events->shouldReceive('dispatch')->once()->with(m::on(function ($event) use ($exception) {
            return $event instanceof CommandFailed
                && $event->command === 'get'
                && $event->parameters === ['key']
                && $event->exception === $exception;
        }));

        $connection = new PhpRedisConnection($client);
        $connection->setEventDispatcher($events);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Test exception');

        $connection->command('get', ['key']);
    }

    public function testCommandExecutedEventIsNotDispatchedWhenCommandFails()
    {
        $exception = new Exception('Test exception');

        $client = m::mock(Redis::class);
        $client->shouldReceive('get')->with('key')->andThrow($exception);

        $events = m::mock(Dispatcher::class);
        $events->shouldReceive('dispatch')->once()->with(m::type(CommandFailed::class));
        $events->shouldNotReceive('dispatch')->with(m::type(CommandExecuted::class));

        $connection = new PhpRedisConnection($client);
        $connection->setEventDispatcher($events);

        try {
            $connection->command('get', ['key']);
        } catch (Exception $e) {
            // Expected exception
        }
    }

    public function testCommandFailedEventContainsConnectionName()
    {
        $exception = new Exception('Test exception');

        $client = m::mock(Redis::class);
        $client->shouldReceive('get')->with('key')->andThrow($exception);

        $events = m::mock(Dispatcher::class);
        $events->shouldReceive('dispatch')->once()->with(m::on(function ($event) {
            return $event instanceof CommandFailed
                && $event->connectionName === 'test-connection';
        }));

        $connection = new PhpRedisConnection($client);
        $connection->setName('test-connection');
        $connection->setEventDispatcher($events);

        try {
            $connection->command('get', ['key']);
        } catch (Exception $e) {
            // Expected exception
        }
    }

    public function testListenForFailuresRegistersCallback()
    {
        $client = m::mock(Redis::class);

        $events = m::mock(Dispatcher::class);
        $events->shouldReceive('listen')->once()->with(CommandFailed::class, m::type('Closure'));

        $connection = new PhpRedisConnection($client);
        $connection->setEventDispatcher($events);

        $connection->listenForFailures(function () {
            // callback
        });
    }
}
