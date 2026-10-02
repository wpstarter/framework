<?php

namespace WpStarter\Support\Facades;

/**
 * @method static \Psr\Log\LoggerInterface build(array $config)
 * @method static \Psr\Log\LoggerInterface stack(array $channels, string|null $channel = null)
 * @method static \Psr\Log\LoggerInterface channel(string|null $channel = null)
 * @method static \Psr\Log\LoggerInterface driver(string|null $driver = null)
 * @method static \WpStarter\Log\LogManager shareContext(array $context)
 * @method static array sharedContext()
 * @method static \WpStarter\Log\LogManager withoutContext(string[]|null $keys = null)
 * @method static \WpStarter\Log\LogManager flushSharedContext()
 * @method static string|null getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static \WpStarter\Log\LogManager extend(string $driver, \Closure $callback)
 * @method static void forgetChannel(string|null $driver = null)
 * @method static array getChannels()
 * @method static void emergency(string|\Stringable $message, array $context = [])
 * @method static void alert(string|\Stringable $message, array $context = [])
 * @method static void critical(string|\Stringable $message, array $context = [])
 * @method static void error(string|\Stringable $message, array $context = [])
 * @method static void warning(string|\Stringable $message, array $context = [])
 * @method static void notice(string|\Stringable $message, array $context = [])
 * @method static void info(string|\Stringable $message, array $context = [])
 * @method static void debug(string|\Stringable $message, array $context = [])
 * @method static void log(mixed $level, string|\Stringable $message, array $context = [])
 * @method static \WpStarter\Log\LogManager setApplication(\WpStarter\Contracts\Foundation\Application $app)
 * @method static void write(string $level, \WpStarter\Contracts\Support\Arrayable|\WpStarter\Contracts\Support\Jsonable|\WpStarter\Support\Stringable|array|string $message, array $context = [])
 * @method static \WpStarter\Log\Logger withContext(array $context = [])
 * @method static void listen(\Closure $callback)
 * @method static \Psr\Log\LoggerInterface getLogger()
 * @method static \WpStarter\Contracts\Events\Dispatcher|null getEventDispatcher()
 * @method static void setEventDispatcher(\WpStarter\Contracts\Events\Dispatcher $dispatcher)
 * @method static \WpStarter\Log\Logger|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \WpStarter\Log\Logger|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 *
 * @see \WpStarter\Log\LogManager
 */
class Log extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'log';
    }
}
