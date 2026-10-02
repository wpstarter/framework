<?php

namespace WpStarter\Session\Middleware;

use BadMethodCallException;
use Closure;
use WpStarter\Auth\AuthenticationException;
use WpStarter\Contracts\Auth\Factory as AuthFactory;
use WpStarter\Contracts\Session\Middleware\AuthenticatesSessions;
use WpStarter\Http\Request;

class AuthenticateSession implements AuthenticatesSessions
{
    /**
     * The authentication factory implementation.
     *
     * @var \WpStarter\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * The callback that should be used to generate the authentication redirect path.
     *
     * @var callable
     */
    protected static $redirectToCallback;

    /**
     * Create a new middleware instance.
     *
     * @param  \WpStarter\Contracts\Auth\Factory  $auth
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
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $request->hasSession() || ! $request->user() || ! $request->user()->getAuthPassword()) {
            return $next($request);
        }

        if ($this->guard()->viaRemember()) {
            $passwordHashFromCookie = explode('|', $request->cookies->get($this->guard()->getRecallerName()))[2] ?? null;

            if (! $passwordHashFromCookie ||
                ! $this->validatePasswordHash($request->user()->getAuthPassword(), $passwordHashFromCookie)) {
                $this->logout($request);
            }
        }

        if (! $request->session()->has('password_hash_'.$this->auth->getDefaultDriver())) {
            $this->storePasswordHashInSession($request);
        }

        $sessionPasswordHash = $request->session()->get('password_hash_'.$this->auth->getDefaultDriver());

        if (! $this->validatePasswordHash($request->user()->getAuthPassword(), $sessionPasswordHash)) {
            $this->logout($request);
        }

        return ws_tap($next($request), function () use ($request) {
            if (! is_null($this->guard()->user())) {
                $this->storePasswordHashInSession($request);
            }
        });
    }

    /**
     * Store the user's current password hash in the session.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return void
     */
    protected function storePasswordHashInSession($request)
    {
        if (! $request->user()) {
            return;
        }

        $passwordHash = $request->user()->getAuthPassword();

        try {
            $passwordHash = $this->guard()->hashPasswordForCookie($passwordHash);
        } catch (BadMethodCallException) {
        }

        $request->session()->put([
            'password_hash_'.$this->auth->getDefaultDriver() => $passwordHash,
        ]);
    }

    /**
     * Validate the password hash against the stored value.
     *
     * @param  string  $passwordHash
     * @param  string  $storedValue
     * @return bool
     */
    protected function validatePasswordHash($passwordHash, $storedValue)
    {
        try {
            // Try new HMAC format first, then fall back to raw password hash format for backward compatibility
            return hash_equals($this->guard()->hashPasswordForCookie($passwordHash), $storedValue)
                || hash_equals($passwordHash, $storedValue);
        } catch (BadMethodCallException) {
            return hash_equals($passwordHash, $storedValue);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return void
     *
     * @throws \WpStarter\Auth\AuthenticationException
     */
    protected function logout($request)
    {
        $this->guard()->logoutCurrentDevice();

        $request->session()->flush();

        throw new AuthenticationException(
            'Unauthenticated.', [$this->auth->getDefaultDriver()], $this->redirectTo($request)
        );
    }

    /**
     * Get the guard instance that should be used by the middleware.
     *
     * @return \WpStarter\Contracts\Auth\Factory|\WpStarter\Contracts\Auth\Guard
     */
    protected function guard()
    {
        return $this->auth;
    }

    /**
     * Get the path the user should be redirected to when their session is not authenticated.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo(Request $request)
    {
        if (static::$redirectToCallback) {
            return call_user_func(static::$redirectToCallback, $request);
        }
    }

    /**
     * Specify the callback that should be used to generate the redirect path.
     *
     * @param  callable  $redirectToCallback
     * @return void
     */
    public static function redirectUsing(callable $redirectToCallback)
    {
        static::$redirectToCallback = $redirectToCallback;
    }
}
