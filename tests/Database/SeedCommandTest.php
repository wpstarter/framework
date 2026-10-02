<?php

namespace WpStarter\Tests\Database;

use WpStarter\Console\Command;
use WpStarter\Console\OutputStyle;
use WpStarter\Console\View\Components\Factory;
use WpStarter\Container\Container;
use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Database\ConnectionResolverInterface;
use WpStarter\Database\Console\Seeds\SeedCommand;
use WpStarter\Database\Console\Seeds\WithoutModelEvents;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Seeder;
use WpStarter\Events\NullDispatcher;
use WpStarter\Testing\Assert;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class SeedCommandTest extends TestCase
{
    public function testHandle()
    {
        $input = new ArrayInput(['--force' => true, '--database' => 'sqlite']);
        $output = new NullOutput;
        $outputStyle = new OutputStyle($input, $output);

        $seeder = m::mock(Seeder::class);
        $seeder->shouldReceive('setContainer')->once()->andReturnSelf();
        $seeder->shouldReceive('setCommand')->once()->andReturnSelf();
        $seeder->shouldReceive('__invoke')->once();

        $resolver = m::mock(ConnectionResolverInterface::class);
        $resolver->shouldReceive('getDefaultConnection')->once();
        $resolver->shouldReceive('setDefaultConnection')->once()->with('sqlite');

        $container = m::mock(Container::class);
        $container->shouldReceive('call');
        $container->shouldReceive('environment')->once()->andReturn('testing');
        $container->shouldReceive('runningUnitTests')->andReturn('true');
        $container->shouldReceive('make')->with('DatabaseSeeder')->andReturn($seeder);
        $container->shouldReceive('make')->with(OutputStyle::class, m::any())->andReturn(
            $outputStyle
        );
        $container->shouldReceive('make')->with(Factory::class, m::any())->andReturn(
            new Factory($outputStyle)
        );

        $command = new SeedCommand($resolver);
        $command->setLaravel($container);

        // call run to set up IO, then fire manually.
        $command->run($input, $output);
        $command->handle();

        $container->shouldHaveReceived('call')->with([$command, 'handle']);
    }

    public function testWithoutModelEvents()
    {
        $input = new ArrayInput([
            '--force' => true,
            '--database' => 'sqlite',
            '--class' => UserWithoutModelEventsSeeder::class,
        ]);
        $output = new NullOutput;
        $outputStyle = new OutputStyle($input, $output);

        $instance = new UserWithoutModelEventsSeeder();

        $seeder = m::mock($instance);
        $seeder->shouldReceive('setContainer')->once()->andReturnSelf();
        $seeder->shouldReceive('setCommand')->once()->andReturnSelf();

        $resolver = m::mock(ConnectionResolverInterface::class);
        $resolver->shouldReceive('getDefaultConnection')->once();
        $resolver->shouldReceive('setDefaultConnection')->once()->with('sqlite');

        $container = m::mock(Container::class);
        $container->shouldReceive('call');
        $container->shouldReceive('environment')->once()->andReturn('testing');
        $container->shouldReceive('runningUnitTests')->andReturn('true');
        $container->shouldReceive('make')->with(UserWithoutModelEventsSeeder::class)->andReturn($seeder);
        $container->shouldReceive('make')->with(OutputStyle::class, m::any())->andReturn(
            $outputStyle
        );
        $container->shouldReceive('make')->with(Factory::class, m::any())->andReturn(
            new Factory($outputStyle)
        );

        $command = new SeedCommand($resolver);
        $command->setLaravel($container);

        Model::setEventDispatcher($dispatcher = m::mock(Dispatcher::class));

        // call run to set up IO, then fire manually.
        $command->run($input, $output);
        $command->handle();

        Assert::assertSame($dispatcher, Model::getEventDispatcher());

        $container->shouldHaveReceived('call')->with([$command, 'handle']);
    }

    public function testProhibitable()
    {
        $input = new ArrayInput([]);
        $output = new NullOutput;
        $outputStyle = new OutputStyle($input, $output);

        $resolver = m::mock(ConnectionResolverInterface::class);

        $container = m::mock(Container::class);
        $container->shouldReceive('call');
        $container->shouldReceive('runningUnitTests')->andReturn('true');
        $container->shouldReceive('make')->with(OutputStyle::class, m::any())->andReturn(
            $outputStyle
        );
        $container->shouldReceive('make')->with(Factory::class, m::any())->andReturn(
            new Factory($outputStyle)
        );

        $command = new SeedCommand($resolver);
        $command->setLaravel($container);

        // call run to set up IO, then fire manually.
        $command->run($input, $output);

        SeedCommand::prohibit();

        Assert::assertSame(Command::FAILURE, $command->handle());
    }

    protected function tearDown(): void
    {
        SeedCommand::prohibit(false);

        Model::unsetEventDispatcher();

        parent::tearDown();
    }
}

class UserWithoutModelEventsSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run()
    {
        Assert::assertInstanceOf(NullDispatcher::class, Model::getEventDispatcher());
    }
}
