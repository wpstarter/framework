<?php

namespace WpStarter\Support\Facades;

use Closure;
use WpStarter\Process\Factory;

/**
 * @method static \WpStarter\Process\PendingProcess command(array|string $command)
 * @method static \WpStarter\Process\PendingProcess path(string $path)
 * @method static \WpStarter\Process\PendingProcess timeout(int $timeout)
 * @method static \WpStarter\Process\PendingProcess idleTimeout(int $timeout)
 * @method static \WpStarter\Process\PendingProcess forever()
 * @method static \WpStarter\Process\PendingProcess env(array $environment)
 * @method static \WpStarter\Process\PendingProcess input(\Traversable|resource|string|int|float|bool|null $input)
 * @method static \WpStarter\Process\PendingProcess quietly()
 * @method static \WpStarter\Process\PendingProcess tty(bool $tty = true)
 * @method static \WpStarter\Process\PendingProcess options(array $options)
 * @method static \WpStarter\Contracts\Process\ProcessResult run(array|string|null $command = null, callable|null $output = null)
 * @method static \WpStarter\Process\InvokedProcess start(array|string|null $command = null, callable|null $output = null)
 * @method static bool supportsTty()
 * @method static \WpStarter\Process\PendingProcess withFakeHandlers(array $fakeHandlers)
 * @method static \WpStarter\Process\PendingProcess|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \WpStarter\Process\PendingProcess|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \WpStarter\Process\FakeProcessResult result(array|string $output = '', array|string $errorOutput = '', int $exitCode = 0)
 * @method static \WpStarter\Process\FakeProcessDescription describe()
 * @method static \WpStarter\Process\FakeProcessSequence sequence(array $processes = [])
 * @method static bool isRecording()
 * @method static \WpStarter\Process\Factory recordIfRecording(\WpStarter\Process\PendingProcess $process, \WpStarter\Contracts\Process\ProcessResult $result)
 * @method static \WpStarter\Process\Factory record(\WpStarter\Process\PendingProcess $process, \WpStarter\Contracts\Process\ProcessResult $result)
 * @method static \WpStarter\Process\Factory preventStrayProcesses(bool $prevent = true)
 * @method static bool preventingStrayProcesses()
 * @method static \WpStarter\Process\Factory assertRan(\Closure|string $callback)
 * @method static \WpStarter\Process\Factory assertRanTimes(\Closure|string $callback, int $times = 1)
 * @method static \WpStarter\Process\Factory assertNotRan(\Closure|string $callback)
 * @method static \WpStarter\Process\Factory assertDidntRun(\Closure|string $callback)
 * @method static \WpStarter\Process\Factory assertNothingRan()
 * @method static \WpStarter\Process\Pool pool(callable $callback)
 * @method static \WpStarter\Contracts\Process\ProcessResult pipe(callable|array $callback, callable|null $output = null)
 * @method static \WpStarter\Process\ProcessPoolResults concurrently(callable $callback, callable|null $output = null)
 * @method static \WpStarter\Process\PendingProcess newPendingProcess()
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 *
 * @see \WpStarter\Process\PendingProcess
 * @see \WpStarter\Process\Factory
 */
class Process extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Factory::class;
    }

    /**
     * Indicate that the process factory should fake processes.
     *
     * @param  \Closure|array|null  $callback
     * @return \WpStarter\Process\Factory
     */
    public static function fake(Closure|array|null $callback = null)
    {
        return tap(static::getFacadeRoot(), function ($fake) use ($callback) {
            static::swap($fake->fake($callback));
        });
    }
}
