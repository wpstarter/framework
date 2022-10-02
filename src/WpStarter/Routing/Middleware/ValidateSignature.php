<?php

namespace WpStarter\Routing\Middleware;

use Closure;
use WpStarter\Routing\Exceptions\InvalidSignatureException;

class ValidateSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $relative
     * @return \WpStarter\Http\Response
     *
     * @throws \WpStarter\Routing\Exceptions\InvalidSignatureException
     */
    public function handle($request, Closure $next, $relative = null)
    {
        if ($request->hasValidSignature($relative !== 'relative')) {
            return $next($request);
        }

        throw new InvalidSignatureException;
    }
}
