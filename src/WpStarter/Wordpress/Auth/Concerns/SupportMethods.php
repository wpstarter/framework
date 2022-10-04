<?php

namespace WpStarter\Wordpress\Auth\Concerns;

trait SupportMethods
{
    protected static $lazyLoadingViolationCallback;
    /**
     * Register a callback that is responsible for handling lazy loading violations.
     *
     * @param  callable|null  $callback
     * @return void
     */
    public static function handleLazyLoadingViolationUsing(?callable $callback)
    {
        static::$lazyLoadingViolationCallback = $callback;
    }
}