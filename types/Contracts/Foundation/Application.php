<?php

use WpStarter\Config\Repository;
use WpStarter\Contracts\Foundation\Application;

use function PHPStan\Testing\assertType;

$app = resolve(Application::class);

assertType('stdClass', $app->instance('foo', new stdClass));

assertType('mixed', $app->get('foo'));
assertType('WpStarter\Config\Repository', $app->get(Repository::class));

assertType('Closure(): mixed', $app->factory('foo'));
assertType('Closure(): WpStarter\Config\Repository', $app->factory(Repository::class));

assertType('mixed', $app->make('foo'));
assertType('WpStarter\Config\Repository', $app->make(Repository::class));
