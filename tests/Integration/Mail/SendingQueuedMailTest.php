<?php

namespace WpStarter\Tests\Integration\Mail;

use WpStarter\Mail\Mailable;
use WpStarter\Mail\SendQueuedMailable;
use WpStarter\Queue\Middleware\RateLimited;
use WpStarter\Support\Facades\Mail;
use WpStarter\Support\Facades\Queue;
use WpStarter\Support\Facades\View;
use Orchestra\Testbench\TestCase;

class SendingQueuedMailTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('mail.driver', 'array');

        View::addLocation(__DIR__.'/Fixtures');
    }

    public function testMailIsSentWithDefaultLocale()
    {
        Queue::fake();

        Mail::to('test@mail.com')->queue(new SendingQueuedMailTestMail);

        Queue::assertPushed(SendQueuedMailable::class, function ($job) {
            return $job->middleware[0] instanceof RateLimited;
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
