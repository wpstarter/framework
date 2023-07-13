<?php

namespace WpStarter\Wordpress\Admin\Routing;

class RouteRegistrar extends \WpStarter\Routing\RouteRegistrar
{
    protected $allowedAttributes=[
        'as',
        'controller',
        'domain',
        'middleware',
        'name',
        'namespace',
        'prefix',
        'scopeBindings',
        'where',
        'withoutMiddleware',
        'parent',
    ];
}