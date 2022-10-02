<?php

namespace WpStarter\Routing;

use WpStarter\Http\RedirectResponse;
use WpStarter\Http\Request;
use WpStarter\Support\Str;

class RedirectController extends Controller
{
    /**
     * Invoke the controller method.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  \WpStarter\Routing\UrlGenerator  $url
     * @return \WpStarter\Http\RedirectResponse
     */
    public function __invoke(Request $request, UrlGenerator $url)
    {
        $parameters = ws_collect($request->route()->parameters());

        $status = $parameters->get('status');

        $destination = $parameters->get('destination');

        $parameters->forget('status')->forget('destination');

        $route = (new Route('GET', $destination, [
            'as' => 'laravel_route_redirect_destination',
        ]))->bind($request);

        $parameters = $parameters->only(
            $route->getCompiled()->getPathVariables()
        )->toArray();

        $url = $url->toRoute($route, $parameters, false);

        if (! Str::startsWith($destination, '/') && Str::startsWith($url, '/')) {
            $url = Str::after($url, '/');
        }

        return new RedirectResponse($url, $status);
    }
}
