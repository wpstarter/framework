<?php

namespace WpStarter\Tests\Testing;

use WpStarter\Testing\ParallelConsoleOutput;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class ParallelConsoleOutputTest extends TestCase
{
    public function testWrite()
    {
        $original = new BufferedOutput;
        $output = new ParallelConsoleOutput($original);

        $output->write('Running phpunit in 12 processes with laravel/laravel.');
        $this->assertEmpty($original->fetch());

        $output->write('Configuration read from phpunit.xml.dist');
        $this->assertEmpty($original->fetch());

        $output->write('... 3/3 (100%)');
        $this->assertSame('... 3/3 (100%)', $original->fetch());
    }
}
