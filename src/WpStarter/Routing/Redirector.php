<?php

namespace WpStarter\Routing;

use WpStarter\Http\RedirectResponse;
use WpStarter\Session\Store as SessionStore;
use WpStarter\Support\Traits\Macroable;

class Redirector
{
    use Macroable;

    /**
     * The URL generator instance.
     *
     * @var \WpStarter\Routing\UrlGenerator
     */
    protected $generator;

    /**
     * The session store instance.
     *
     * @var \WpStarter\Session\Store
     */
    protected $session;

    /**
     * Create a new Redirector instance.
     *
     * @param  \WpStarter\Routing\UrlGenerator  $generator
     */
    public function __construct(UrlGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Create a new redirect response to the previous location.
     *
     * @param  int  $status
     * @param  array  $headers
     * @param  mixed  $fallback
     * @return \WpStarter\Http\RedirectResponse
     */
    public function back($status = 302, $headers = [], $fallback = false)
    {
        return $this->createRedirect($this->generator->previous($fallback), $status, $headers);
    }

    /**
     * Create a new redirect response to the current URI.
     *
     * @param  int  $status
     * @param  array  $headers
     * @return \WpStarter\Http\RedirectResponse
     */
    public function refresh($status = 302, $headers = [])
    {
        return $this->to($this->generator->getRequest()->path(), $status, $headers);
    }

    /**
     * Create a new redirect response, while putting the current URL in the session.
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \WpStarter\Http\RedirectResponse
     */
    public function guest($path, $status = 302, $headers = [], $secure = null)
    {
        $request = $this->generator->getRequest();

        $intended = $request->isMethod('GET') && $request->route() && ! $request->expectsJson()
            ? $this->generator->full()
            : $this->generator->previous();

        if ($intended) {
            $this->setIntendedUrl($intended);
        }

        return $this->to($path, $status, $headers, $secure);
    }

    /**
     * Create a new redirect response to the previously intended location.
     *
     * @param  mixed  $default
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \WpStarter\Http\RedirectResponse
     */
    public function intended($default = '/', $status = 302, $headers = [], $secure = null)
    {
        $path = $this->session->pull('url.intended', $default);

        return $this->to($path, $status, $headers, $secure);
    }

    /**
     * Create a new redirect response to the given path.
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @param  bool|null  $secure
     * @return \WpStarter\Http\RedirectResponse
     */
    public function to($path, $status = 302, $headers = [], $secure = null)
    {
        return $this->createRedirect($this->generator->to($path, [], $secure), $status, $headers);
    }

    /**
     * Create a new redirect response to an external URL (no validation).
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @return \WpStarter\Http\RedirectResponse
     */
    public function away($path, $status = 302, $headers = [])
    {
        return $this->createRedirect($path, $status, $headers);
    }

    /**
     * Create a new redirect response to the given HTTPS path.
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @return \WpStarter\Http\RedirectResponse
     */
    public function secure($path, $status = 302, $headers = [])
    {
        return $this->to($path, $status, $headers, true);
    }

    /**
     * Create a new redirect response to a named route.
     *
     * @param  \BackedEnum|string  $route
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \WpStarter\Http\RedirectResponse
     */
    public function route($route, $parameters = [], $status = 302, $headers = [])
    {
        return $this->to($this->generator->route($route, $parameters), $status, $headers);
    }

    /**
     * Create a new redirect response to a signed named route.
     *
     * @param  \BackedEnum|string  $route
     * @param  mixed  $parameters
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
     * @param  int  $status
     * @param  array  $headers
     * @return \WpStarter\Http\RedirectResponse
     */
    public function signedRoute($route, $parameters = [], $expiration = null, $status = 302, $headers = [])
    {
        return $this->to($this->generator->signedRoute($route, $parameters, $expiration), $status, $headers);
    }

    /**
     * Create a new redirect response to a signed named route.
     *
     * @param  \BackedEnum|string  $route
     * @param  \DateTimeInterface|\DateInterval|int|null  $expiration
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \WpStarter\Http\RedirectResponse
     */
    public function temporarySignedRoute($route, $expiration, $parameters = [], $status = 302, $headers = [])
    {
        return $this->to($this->generator->temporarySignedRoute($route, $expiration, $parameters), $status, $headers);
    }

    /**
     * Create a new redirect response to a controller action.
     *
     * @param  string|array  $action
     * @param  mixed  $parameters
     * @param  int  $status
     * @param  array  $headers
     * @return \WpStarter\Http\RedirectResponse
     */
    public function action($action, $parameters = [], $status = 302, $headers = [])
    {
        return $this->to($this->generator->action($action, $parameters), $status, $headers);
    }

    /**
     * Create a new redirect response.
     *
     * @param  string  $path
     * @param  int  $status
     * @param  array  $headers
     * @return \WpStarter\Http\RedirectResponse
     */
    protected function createRedirect($path, $status, $headers)
    {
        return ws_tap(new RedirectResponse($path, $status, $headers), function ($redirect) {
            if (isset($this->session)) {
                $redirect->setSession($this->session);
            }

            $redirect->setRequest($this->generator->getRequest());
        });
    }

    /**
     * Get the URL generator instance.
     *
     * @return \WpStarter\Routing\UrlGenerator
     */
    public function getUrlGenerator()
    {
        return $this->generator;
    }

    /**
     * Set the active session store.
     *
     * @param  \WpStarter\Session\Store  $session
     * @return void
     */
    public function setSession(SessionStore $session)
    {
        $this->session = $session;
    }

    /**
     * Get the "intended" URL from the session.
     *
     * @return string|null
     */
    public function getIntendedUrl()
    {
        return $this->session->get('url.intended');
    }

    /**
     * Set the "intended" URL in the session.
     *
     * @param  string  $url
     * @return $this
     */
    public function setIntendedUrl($url)
    {
        $this->session->put('url.intended', $url);

        return $this;
    }
}
