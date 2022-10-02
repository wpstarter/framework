<?php

namespace WpStarter\Support\Facades;

use WpStarter\Contracts\Broadcasting\Factory as BroadcastingFactoryContract;

/**
 * @method static \WpStarter\Broadcasting\Broadcasters\Broadcaster channel(string $channel, callable|string  $callback, array $options = [])
 * @method static mixed auth(\WpStarter\Http\Request $request)
 * @method static \WpStarter\Contracts\Broadcasting\Broadcaster connection($name = null);
 * @method static void routes(array $attributes = null)
 * @method static \WpStarter\Broadcasting\BroadcastManager socket($request = null)
 *
 * @see \WpStarter\Contracts\Broadcasting\Factory
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
