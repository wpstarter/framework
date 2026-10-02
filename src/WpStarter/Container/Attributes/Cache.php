<?php

namespace WpStarter\Container\Attributes;

use Attribute;
use WpStarter\Contracts\Container\Container;
use WpStarter\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Cache implements ContextualAttribute
{
    /**
     * Create a new class instance.
     */
    public function __construct(public ?string $store = null)
    {
    }

    /**
     * Resolve the cache store.
     *
     * @param  self  $attribute
     * @param  \WpStarter\Contracts\Container\Container  $container
     * @return \WpStarter\Contracts\Cache\Repository
     */
    public static function resolve(self $attribute, Container $container)
    {
        return $container->make('cache')->store($attribute->store);
    }
}
