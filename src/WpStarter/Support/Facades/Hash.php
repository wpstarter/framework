<?php

namespace WpStarter\Support\Facades;

/**
 * @method static \WpStarter\Hashing\BcryptHasher createBcryptDriver()
 * @method static \WpStarter\Hashing\ArgonHasher createArgonDriver()
 * @method static \WpStarter\Hashing\Argon2IdHasher createArgon2idDriver()
 * @method static array info(string $hashedValue)
 * @method static string make(string $value, array $options = [])
 * @method static bool check(string $value, string $hashedValue, array $options = [])
 * @method static bool needsRehash(string $hashedValue, array $options = [])
 * @method static bool isHashed(string $value)
 * @method static string getDefaultDriver()
 * @method static mixed driver(string|null $driver = null)
 * @method static \WpStarter\Hashing\HashManager extend(string $driver, \Closure $callback)
 * @method static array getDrivers()
 * @method static \WpStarter\Contracts\Container\Container getContainer()
 * @method static \WpStarter\Hashing\HashManager setContainer(\WpStarter\Contracts\Container\Container $container)
 * @method static \WpStarter\Hashing\HashManager forgetDrivers()
 *
 * @see \WpStarter\Hashing\HashManager
 * @see \WpStarter\Hashing\AbstractHasher
 */
class Hash extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'hash';
    }
}
