<?php

namespace WpStarter\Routing;

use WpStarter\Contracts\Routing\ResponseFactory;

class ViewController extends Controller
{
    /**
     * The response factory implementation.
     *
     * @var \WpStarter\Contracts\Routing\ResponseFactory
     */
    protected $response;

    /**
     * Create a new controller instance.
     *
     * @param  \WpStarter\Contracts\Routing\ResponseFactory  $response
     */
    public function __construct(ResponseFactory $response)
    {
        $this->response = $response;
    }

    /**
     * Invoke the controller method.
     *
     * @param  mixed  ...$args
     * @return \WpStarter\Http\Response
     */
    public function __invoke(...$args)
    {
        $routeParameters = array_filter($args, function ($key) {
            return ! in_array($key, ['view', 'data', 'status', 'headers']);
        }, ARRAY_FILTER_USE_KEY);

        $args['data'] = array_merge($args['data'], $routeParameters);

        return $this->response->view(
            $args['view'],
            $args['data'],
            $args['status'],
            $args['headers']
        );
    }

    /**
     * Execute an action on the controller.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function callAction($method, $parameters)
    {
        return $this->{$method}(...$parameters);
    }
}
