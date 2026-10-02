<?php

namespace WpStarter\Support\Facades;

/**
 * @method static bool has(string $key)
 * @method static bool missing(string $key)
 * @method static bool hasHidden(string $key)
 * @method static bool missingHidden(string $key)
 * @method static array all()
 * @method static array allHidden()
 * @method static mixed get(string $key, mixed $default = null)
 * @method static mixed getHidden(string $key, mixed $default = null)
 * @method static mixed pull(string $key, mixed $default = null)
 * @method static mixed pullHidden(string $key, mixed $default = null)
 * @method static array only(array $keys)
 * @method static array onlyHidden(array $keys)
 * @method static array except(array $keys)
 * @method static array exceptHidden(array $keys)
 * @method static \WpStarter\Log\Context\Repository add(string|array $key, mixed $value = null)
 * @method static \WpStarter\Log\Context\Repository addHidden(string|array $key, mixed $value = null)
 * @method static mixed remember(string $key, mixed $value)
 * @method static mixed rememberHidden(string $key, mixed $value)
 * @method static \WpStarter\Log\Context\Repository forget(string|array $key)
 * @method static \WpStarter\Log\Context\Repository forgetHidden(string|array $key)
 * @method static \WpStarter\Log\Context\Repository addIf(string $key, mixed $value)
 * @method static \WpStarter\Log\Context\Repository addHiddenIf(string $key, mixed $value)
 * @method static \WpStarter\Log\Context\Repository push(string $key, mixed ...$values)
 * @method static mixed pop(string $key)
 * @method static \WpStarter\Log\Context\Repository pushHidden(string $key, mixed ...$values)
 * @method static mixed popHidden(string $key)
 * @method static \WpStarter\Log\Context\Repository increment(string $key, int $amount = 1)
 * @method static \WpStarter\Log\Context\Repository decrement(string $key, int $amount = 1)
 * @method static bool stackContains(string $key, mixed $value, bool $strict = false)
 * @method static bool hiddenStackContains(string $key, mixed $value, bool $strict = false)
 * @method static mixed scope(callable $callback, array $data = [], array $hidden = [])
 * @method static bool isEmpty()
 * @method static \WpStarter\Log\Context\Repository dehydrating(callable $callback)
 * @method static \WpStarter\Log\Context\Repository hydrated(callable $callback)
 * @method static \WpStarter\Log\Context\Repository handleUnserializeExceptionsUsing(callable|null $callback)
 * @method static \WpStarter\Log\Context\Repository flush()
 * @method static \WpStarter\Log\Context\Repository|mixed when(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static \WpStarter\Log\Context\Repository|mixed unless(\Closure|mixed|null $value = null, callable|null $callback = null, callable|null $default = null)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static \WpStarter\Database\Eloquent\Contracts\Model restoreModel(\WpStarter\Contracts\Database\ModelIdentifier $value)
 *
 * @see \WpStarter\Log\Context\Repository
 */
class Context extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \WpStarter\Log\Context\Repository::class;
    }
}
