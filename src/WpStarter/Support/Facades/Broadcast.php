<?php

namespace WpStarter\Support\Facades;

use WpStarter\Contracts\Broadcasting\Factory as BroadcastingFactoryContract;

/**
 * @method static void routes(array|null $attributes = null)
 * @method static void userRoutes(array|null $attributes = null)
 * @method static void channelRoutes(array|null $attributes = null)
 * @method static string|null socket(\WpStarter\Http\Request|null $request = null)
 * @method static \WpStarter\Broadcasting\AnonymousEvent on(\WpStarter\Broadcasting\Channel|array|string $channels)
 * @method static \WpStarter\Broadcasting\AnonymousEvent private(string $channel)
 * @method static \WpStarter\Broadcasting\AnonymousEvent presence(string $channel)
 * @method static \WpStarter\Broadcasting\PendingBroadcast event(mixed $event = null)
 * @method static void queue(mixed $event)
 * @method static mixed connection(string|null $driver = null)
 * @method static mixed driver(string|null $name = null)
 * @method static \Pusher\Pusher pusher(array $config)
 * @method static \Ably\AblyRest ably(array $config)
 * @method static string getDefaultDriver()
 * @method static void setDefaultDriver(string $name)
 * @method static void purge(string|null $name = null)
 * @method static \WpStarter\Broadcasting\BroadcastManager extend(string $driver, \Closure $callback)
 * @method static \WpStarter\Contracts\Foundation\Application getApplication()
 * @method static \WpStarter\Broadcasting\BroadcastManager setApplication(\WpStarter\Contracts\Foundation\Application $app)
 * @method static \WpStarter\Broadcasting\BroadcastManager forgetDrivers()
 * @method static mixed auth(\WpStarter\Http\Request $request)
 * @method static mixed validAuthenticationResponse(\WpStarter\Http\Request $request, mixed $result)
 * @method static void broadcast(array $channels, string $event, array $payload = [])
 * @method static array|null resolveAuthenticatedUser(\WpStarter\Http\Request $request)
 * @method static void resolveAuthenticatedUserUsing(\Closure $callback)
 * @method static \WpStarter\Broadcasting\Broadcasters\Broadcaster channel(\WpStarter\Contracts\Broadcasting\HasBroadcastChannel|string $channel, callable|string $callback, array $options = [])
 * @method static \WpStarter\Support\Collection getChannels()
 *
 * @see \WpStarter\Broadcasting\BroadcastManager
 * @see \WpStarter\Broadcasting\Broadcasters\Broadcaster
 */
class Broadcast extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return BroadcastingFactoryContract::class;
    }
}
