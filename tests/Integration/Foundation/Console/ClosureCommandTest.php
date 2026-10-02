<?php

namespace WpStarter\Tests\Integration\Foundation\Console;

use WpStarter\Support\Facades\Artisan;
use Orchestra\Testbench\TestCase;

class ClosureCommandTest extends TestCase
{
    /** {@inheritDoc} */
    #[\Override]
    protected function defineEnvironment($app)
    {
        Artisan::command('inspire', function () {
            $this->comment('We must ship. - Taylor Otwell');
        })->purpose('Display an inspiring quote');
    }

    public function testItCanRunClosureCommand()
    {
        $this->artisan('inspire')->expectsOutput('We must ship. - Taylor Otwell');
    }
}
