<?php

namespace WpStarter\Contracts\Auth\Access;

interface Gate
{
    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function allows($ability, $arguments = []);

    /**
     * Determine if the given ability should be denied for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function denies($ability, $arguments = []);

    /**
     * Determine if all of the given abilities should be granted for the current user.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function check($abilities, $arguments = []);

    /**
     * Determine if any one of the given abilities should be granted for the current user.
     *
     * @param  iterable|string  $abilities
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function any($abilities, $arguments = []);

    /**
     * Determine if the given ability should be granted for the current user.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return \WpStarter\Auth\Access\Response
     *
     * @throws \WpStarter\Auth\Access\AuthorizationException
     */
    public function authorize($ability, $arguments = []);

    /**
     * Inspect the user for the given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return \WpStarter\Auth\Access\Response
     */
    public function inspect($ability, $arguments = []);

    /**
     * Get the raw result from the authorization callback.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return mixed
     *
     * @throws \WpStarter\Auth\Access\AuthorizationException
     */
    public function raw($ability, $arguments = []);


    /**
     * Get a guard instance for the given user.
     *
     * @param  \WpStarter\Wordpress\User|mixed  $user
     * @return static
     */
    public function forUser($user);

}
