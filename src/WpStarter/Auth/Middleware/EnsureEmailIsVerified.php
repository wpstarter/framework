<?php

namespace WpStarter\Auth\Middleware;

use Closure;
use WpStarter\Contracts\Auth\MustVerifyEmail;
use WpStarter\Support\Facades\Redirect;
use WpStarter\Support\Facades\URL;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \WpStarter\Http\Response|\WpStarter\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        if (! $request->user() ||
            ($request->user() instanceof MustVerifyEmail &&
            ! $request->user()->hasVerifiedEmail())) {
            return $request->expectsJson()
                    ? ws_abort(403, 'Your email address is not verified.')
                    : Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));
        }

        return $next($request);
    }
}
