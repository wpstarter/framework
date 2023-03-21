<?php

namespace WpStarter\Tests\Routing;

use WpStarter\Container\Container;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Routing\ImplicitRouteBinding;
use WpStarter\Routing\Route;
use PHPUnit\Framework\TestCase;

class ImplicitRouteBindingTest extends TestCase
{
    public function test_it_can_resolve_the_implicit_route_bindings_for_the_given_route()
    {
        $this->expectNotToPerformAssertions();

        $action = ['uses' => function (ImplicitRouteBindingUser $user) {
            return $user;
        }];

        $route = new Route('GET', '/test', $action);
        $route->parameters = ['user' => new ImplicitRouteBindingUser];

        $route->prepareForSerialization();

        $container = Container::getInstance();

        ImplicitRouteBinding::resolveForRoute($container, $route);
    }
}

class ImplicitRouteBindingUser extends Model
{
    //
}
