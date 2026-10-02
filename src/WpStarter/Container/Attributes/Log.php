<?php

namespace WpStarter\Container\Attributes;

use Attribute;
use WpStarter\Contracts\Container\Container;
use WpStarter\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Log implements ContextualAttribute
{
    /**
     * Create a new class instance.
     */
    public function __construct(public ?string $channel = null)
    {
    }

    /**
     * Resolve the log channel.
     *
     * @param  self  $attribute
     * @param  \WpStarter\Contracts\Container\Container  $container
     * @return \Psr\Log\LoggerInterface
     */
    public static function resolve(self $attribute, Container $container)
    {
        return $container->make('log')->channel($attribute->channel);
    }
}
