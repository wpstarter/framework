<?php

namespace WpStarter\Tests\Integration\Mail;

use WpStarter\Mail\Mailable;
use WpStarter\Mail\SendQueuedMailable;
use WpStarter\Queue\Middleware\RateLimited;
use WpStarter\Support\Facades\Mail;
use WpStarter\Support\Facades\Queue;
use Orchestra\Testbench\TestCase;

class SendingQueuedMailTest extends TestCase
{
    protected function defineEnvironment($app)
    {
        $app['config']->set('mail.driver', 'array');

        $app['view']->addLocation(__DIR__.'/Fixtures');
    }

    public function testMailIsSentWithDefaultLocale()
    {
        Queue::fake();

        Mail::to('test@mail.com')->queue(new SendingQueuedMailTestMail);

        Queue::assertPushed(SendQueuedMailable::class, function ($job) {
            return $job->middleware[0] instanceof RateLimited;
        });
    }

    public function testMailIsSentWithDelay()
    {
        Queue::fake();

        $delay = now()->addMinutes(10);

        Mail::to('test@mail.com')->later($delay, new SendingQueuedMailTestMail);

        Queue::assertPushed(SendQueuedMailable::class, function ($job) use ($delay) {
            return $job->delay === $delay;
        });
    }
}

class SendingQueuedMailTestMail extends Mailable
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('view');
    }

    public function middleware()
    {
        return [new RateLimited('limiter')];
    }
}
