<?php

namespace WpStarter\Tests\Integration\Console\Events;

use WpStarter\Contracts\Broadcasting\ShouldBroadcast;
use WpStarter\Contracts\Queue\ShouldQueue;
use WpStarter\Events\Dispatcher;
use WpStarter\Foundation\Console\EventListCommand;
use WpStarter\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;

class EventListCommandTest extends TestCase
{
    public $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = new Dispatcher();
        EventListCommand::resolveEventsUsing(fn () => $this->dispatcher);
    }

    public function testDisplayEmptyList()
    {
        $this->artisan(EventListCommand::class)
            ->assertSuccessful()
            ->expectsOutputToContain("Your application doesn't have any events matching the given criteria.");
    }

    public function testDisplayEvents()
    {
        $this->dispatcher->subscribe(ExampleSubscriber::class);
        $this->dispatcher->listen(ExampleEvent::class, ExampleListener::class);
        $this->dispatcher->listen(ExampleEvent::class, ExampleQueueListener::class);
        $this->dispatcher->listen(ExampleBroadcastEvent::class, ExampleBroadcastListener::class);
        $this->dispatcher->listen(ExampleEvent::class, fn () => '');
        $closureLineNumber = __LINE__ - 1;
        $unixFilePath = str_replace('\\', '/', __FILE__);

        $this->artisan(EventListCommand::class)
            ->assertSuccessful()
            ->expectsOutputToContain('ExampleSubscriberEventName')
            ->expectsOutputToContain('⇂ WpStarter\Tests\Integration\Console\Events\ExampleSubscriber@a')
            ->expectsOutputToContain('WpStarter\Tests\Integration\Console\Events\ExampleBroadcastEvent (ShouldBroadcast)')
            ->expectsOutputToContain('⇂ WpStarter\Tests\Integration\Console\Events\ExampleBroadcastListener')
            ->expectsOutputToContain('WpStarter\Tests\Integration\Console\Events\ExampleEvent')
            ->expectsOutputToContain('⇂ Closure at: '.$unixFilePath.':'.$closureLineNumber);
    }

    public function testDisplayFilteredEvent()
    {
        $this->dispatcher->subscribe(ExampleSubscriber::class);
        $this->dispatcher->listen(ExampleEvent::class, ExampleListener::class);

        $this->artisan(EventListCommand::class, ['--event' => 'ExampleEvent'])
            ->assertSuccessful()
            ->doesntExpectOutput('  ExampleSubscriberEventName')
            ->expectsOutputToContain('ExampleEvent');
    }

    public function testDisplayEmptyListAsJson()
    {
        $this->withoutMockingConsoleOutput()->artisan(EventListCommand::class, ['--json' => true]);
        $output = Artisan::output();

        $this->assertJson($output);
        $this->assertJsonStringEqualsJsonString('[]', $output);
    }

    public function testDisplayEventsAsJson()
    {
        $this->dispatcher->subscribe(ExampleSubscriber::class);
        $this->dispatcher->listen(ExampleEvent::class, ExampleListener::class);
        $this->dispatcher->listen(ExampleEvent::class, ExampleQueueListener::class);
        $this->dispatcher->listen(ExampleBroadcastEvent::class, ExampleBroadcastListener::class);
        $this->dispatcher->listen(ExampleEvent::class, fn () => '');
        $closureLineNumber = __LINE__ - 1;
        $unixFilePath = str_replace('\\', '/', __FILE__);

        $this->withoutMockingConsoleOutput()->artisan(EventListCommand::class, ['--json' => true]);
        $output = Artisan::output();

        $this->assertJson($output);
        $this->assertStringContainsString('ExampleSubscriberEventName', $output);
        $this->assertStringContainsString(json_encode('WpStarter\Tests\Integration\Console\Events\ExampleSubscriber@a'), $output);
        $this->assertStringContainsString(json_encode('WpStarter\Tests\Integration\Console\Events\ExampleBroadcastEvent (ShouldBroadcast)'), $output);
        $this->assertStringContainsString(json_encode('WpStarter\Tests\Integration\Console\Events\ExampleBroadcastListener'), $output);
        $this->assertStringContainsString(json_encode('WpStarter\Tests\Integration\Console\Events\ExampleEvent'), $output);
        $this->assertStringContainsString(json_encode('Closure at: '.$unixFilePath.':'.$closureLineNumber), $output);
    }

    public function testDisplayFilteredEventAsJson()
    {
        $this->dispatcher->subscribe(ExampleSubscriber::class);
        $this->dispatcher->listen(ExampleEvent::class, ExampleListener::class);

        $this->withoutMockingConsoleOutput()->artisan(EventListCommand::class, [
            '--event' => 'ExampleEvent',
            '--json' => true,
        ]);
        $output = Artisan::output();

        $this->assertJson($output);
        $this->assertStringContainsString('ExampleEvent', $output);
        $this->assertStringContainsString('ExampleListener', $output);
        $this->assertStringNotContainsString('ExampleSubscriberEventName', $output);
    }

    protected function tearDown(): void
    {
        EventListCommand::resolveEventsUsing(null);

        parent::tearDown();
    }
}

class ExampleSubscriber
{
    public function subscribe()
    {
        return [
            'ExampleSubscriberEventName' => [
                self::class.'@a',
                self::class.'@b',
            ],
        ];
    }

    public function a()
    {
    }

    public function b()
    {
    }
}

class ExampleEvent
{
}

class ExampleBroadcastEvent implements ShouldBroadcast
{
    public function broadcastOn()
    {
        //
    }
}

class ExampleListener
{
    public function handle()
    {
    }
}

class ExampleQueueListener implements ShouldQueue
{
    public function handle()
    {
    }
}

class ExampleBroadcastListener
{
    public function handle()
    {
        //
    }
}
