<?php

namespace WpStarter\Tests\Testing\Console;

use WpStarter\Foundation\Console\ConfigShowCommand;
use Orchestra\Testbench\TestCase;

class ConfigShowCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        putenv('COLUMNS=64');
    }

    public function testDisplayConfig()
    {
        ws_config()->set('test', [
            'string' => 'Test',
            'int' => 1,
            'float' => 1.2,
            'boolean' => true,
            'null' => null,
            'array' => [
                ConfigShowCommand::class,
            ],
            'empty_array' => [],
            'assoc_array' => ['foo' => 'bar'],
            'class' => new \stdClass,
        ]);

        $this->artisan(ConfigShowCommand::class, ['config' => 'test'])
            ->assertSuccessful()
            ->expectsOutput('  test .......................................................  ')
            ->expectsOutput('  string ................................................ Test  ')
            ->expectsOutput('  int ...................................................... 1  ')
            ->expectsOutput('  float .................................................. 1.2  ')
            ->expectsOutput('  boolean ............................................... true  ')
            ->expectsOutput('  null .................................................. null  ')
            ->expectsOutput('  array ⇁ 0 ... WpStarter\Foundation\Console\ConfigShowCommand  ')
            ->expectsOutput('  empty_array ............................................. []  ')
            ->expectsOutput('  assoc_array ⇁ foo ...................................... bar  ')
            ->expectsOutput('  class ............................................. stdClass  ');
    }

    public function testDisplayNestedConfigItems()
    {
        ws_config()->set('test', [
            'nested' => [
                'foo' => 'bar',
            ],
        ]);

        $this->artisan(ConfigShowCommand::class, ['config' => 'test.nested'])
            ->assertSuccessful()
            ->expectsOutput('  test.nested ................................................  ')
            ->expectsOutput('  foo .................................................... bar  ');
    }

    public function testDisplaySingleValue()
    {
        ws_config()->set('foo', 'bar');

        $this->artisan(ConfigShowCommand::class, ['config' => 'foo'])
            ->assertSuccessful()
            ->expectsOutput('  foo .................................................... bar  ');
    }

    public function testDisplayErrorIfConfigDoesNotExist()
    {
        $this->artisan(ConfigShowCommand::class, ['config' => 'invalid'])
            ->assertFailed();
    }
}
