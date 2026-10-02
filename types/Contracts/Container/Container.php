<?php

use WpStarter\Config\Repository;
use WpStarter\Contracts\Container\Container;
use WpStarter\Http\Request;

use function PHPStan\Testing\assertType;

$container = resolve(Container::class);

assertType('stdClass', $container->instance('foo', new stdClass));

assertType('mixed', $container->get('foo'));
assertType('WpStarter\Config\Repository', $container->get(Repository::class));

assertType('Closure(): mixed', $container->factory('foo'));
assertType('Closure(): WpStarter\Config\Repository', $container->factory(Repository::class));

assertType('mixed', $container->make('foo'));
assertType('WpStarter\Config\Repository', $container->make(Repository::class));

assertType('WpStarter\Http\Request', $container->instance('request', Request::capture()));
