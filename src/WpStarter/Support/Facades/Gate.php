<?php

namespace WpStarter\Support\Facades;

use WpStarter\Contracts\Auth\Access\Gate as GateContract;

/**
 * @method static \WpStarter\Auth\Access\Response authorize(string $ability, array|mixed $arguments = [])
 * @method static \WpStarter\Auth\Access\Response inspect(string $ability, array|mixed $arguments = [])
 * @method static \WpStarter\Contracts\Auth\Access\Gate forUser(\WpStarter\Wordpress\User|mixed $user)
 * @method static bool allows(string $ability, array|mixed $arguments = [])
 * @method static bool any(iterable|string $abilities, array|mixed $arguments = [])
 * @method static bool check(iterable|string $abilities, array|mixed $arguments = [])
 * @method static bool denies(string $ability, array|mixed $arguments = [])
 * @method static mixed raw(string $ability, array|mixed $arguments = [])
 *
 * @see \WpStarter\Contracts\Auth\Access\Gate
 */
class Gate extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return GateContract::class;
    }
}
