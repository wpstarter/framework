<?php

namespace WpStarter\Auth\Access;

use Closure;
use Exception;
use WpStarter\Contracts\Auth\Access\Gate as GateContract;
use WpStarter\Contracts\Container\Container;
use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Support\Arr;
use WpStarter\Support\Collection;
use WpStarter\Support\Str;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionFunction;

class Gate implements GateContract
{
    use HandlesAuthorization;

    /**
     * The container instance.
     *
     * @var \WpStarter\Contracts\Container\Container
     */
    protected $container;

    /**
     * The user resolver callable.
     *
     * @var callable
     */
    protected $userResolver;


    /**
     * Create a new gate instance.
     *
     * @param  \WpStarter\Contracts\Container\Container  $container
     * @param  callable  $userResolver
     * @param  array  $abilities
     * @param  array  $policies
     * @param  array  $beforeCallbacks
     * @param  array  $afterCallbacks
     * @param  callable|null  $guessPolicyNamesUsingCallback
     * @return void
     */
    public function __construct(Container $container, callable $userResolver)
    {
        $this->container = $container;
        $this->userResolver = $userResolver;
    }


    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function allows($ability, $arguments = [])
    {
        return $this->check($ability, $arguments);
    }

    /**
     * Determine if the given ability should be denied for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function denies($ability, $arguments = [])
    {
        return ! $this->allows($ability, $arguments);
    }

    /**
     * Determine if all of the given abilities should be granted for the current user.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function check($abilities, $arguments = [])
    {
        return ws_collect($abilities)->every(function ($ability) use ($arguments) {
            return $this->inspect($ability, $arguments)->allowed();
        });
    }

    /**
     * Determine if any one of the given abilities should be granted for the current user.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function any($abilities, $arguments = [])
    {
        return ws_collect($abilities)->contains(function ($ability) use ($arguments) {
            return $this->check($ability, $arguments);
        });
    }

    /**
     * Determine if all of the given abilities should be denied for the current user.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function none($abilities, $arguments = [])
    {
        return ! $this->any($abilities, $arguments);
    }

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return \WpStarter\Auth\Access\Response
     *
     * @throws \WpStarter\Auth\Access\AuthorizationException
     */
    public function authorize($ability, $arguments = [])
    {
        return $this->inspect($ability, $arguments)->authorize();
    }

    /**
     * Inspect the user for the given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return \WpStarter\Auth\Access\Response
     */
    public function inspect($ability, $arguments = [])
    {
        try {
            $result = $this->raw($ability, $arguments);

            if ($result instanceof Response) {
                return $result;
            }

            return $result ? Response::allow() : Response::deny();
        } catch (AuthorizationException $e) {
            return $e->toResponse();
        }
    }

    /**
     * Get the raw result from the authorization callback.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return mixed
     *
     * @throws \WpStarter\Auth\Access\AuthorizationException
     */
    public function raw($ability, $arguments = [])
    {
        $arguments = Arr::wrap($arguments);

        $user = $this->resolveUser();


        $result = $this->callAuthCallback($user, $ability, $arguments);

        return $result;
    }



    /**
     * Resolve and call the appropriate authorization callback.
     *
     * @param  \WpStarter\Wordpress\User|null  $user
     * @param  string  $ability
     * @param  array  $arguments
     * @return bool
     */
    protected function callAuthCallback($user, $ability, array $arguments)
    {
        return user_can($user,$ability,...$arguments);
    }


    /**
     * Get a gate instance for the given user.
     *
     * @param  \WpStarter\Wordpress\User|mixed  $user
     * @return static
     */
    public function forUser($user)
    {
        $callback = function () use ($user) {
            return $user;
        };

        return new static(
            $this->container, $callback
        );
    }

    /**
     * Resolve the user from the user resolver.
     *
     * @return mixed
     */
    protected function resolveUser()
    {
        return call_user_func($this->userResolver);
    }


    /**
     * Set the container instance used by the gate.
     *
     * @param  \WpStarter\Contracts\Container\Container  $container
     * @return $this
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;

        return $this;
    }
}
