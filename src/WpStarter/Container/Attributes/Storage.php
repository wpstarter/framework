<?php

namespace WpStarter\Container\Attributes;

use Attribute;
use WpStarter\Contracts\Container\Container;
use WpStarter\Contracts\Container\ContextualAttribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class Storage implements ContextualAttribute
{
    /**
     * Create a new class instance.
     */
    public function __construct(public ?string $disk = null)
    {
    }

    /**
     * Resolve the storage disk.
     *
     * @param  self  $attribute
     * @param  \WpStarter\Contracts\Container\Container  $container
     * @return \WpStarter\Contracts\Filesystem\Filesystem
     */
    public static function resolve(self $attribute, Container $container)
    {
        return $container->make('filesystem')->disk($attribute->disk);
    }
}
