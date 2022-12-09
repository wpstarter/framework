<?php

namespace WpStarter\Auth\Middleware;

use Closure;
use WpStarter\Auth\AuthenticationException;
use WpStarter\Contracts\Auth\Factory as Auth;
use WpStarter\Contracts\Auth\Middleware\AuthenticatesRequests;

class Authenticate implements AuthenticatesRequests
{
    /**
     * The authentication factory instance.
     *
     * @var \WpStarter\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \WpStarter\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \WpStarter\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        return $next($request);
    }

    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \WpStarter\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                return $this->auth->shouldUse($guard);
            }
        }

        $this->unauthenticated($request, $guards);
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \WpStarter\Auth\AuthenticationException
     */
    protected function unauthenticated($request, array $guards)
    {
        throw new AuthenticationException(
            'Unauthenticated.', $guards, $this->redirectTo($request)
        );
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        //
    }
}
