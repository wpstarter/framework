<?php

namespace WpStarter\Auth\Middleware;

use Closure;
use WpStarter\Contracts\Auth\Factory as AuthFactory;

class AuthenticateWithBasicAuth
{
    /**
     * The guard factory instance.
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
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @param  string|null  $field
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function handle($request, Closure $next, $guard = null, $field = null)
    {
        $this->auth->guard($guard)->basic($field ?: 'email');

        return $next($request);
    }
}
