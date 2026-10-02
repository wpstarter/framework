<?php

use function PHPStan\Testing\assertType;

/** @var User $user */
/** @var \WpStarter\Contracts\Database\Eloquent\CastsAttributes<\WpStarter\Support\Stringable, string|\Stringable> $cast */
assertType('WpStarter\Support\Stringable|null', $cast->get($user, 'email', 'taylor@laravel.com', $user->getAttributes()));

$cast->set($user, 'email', 'taylor@laravel.com', $user->getAttributes()); // This works.
$cast->set($user, 'email', \WpStarter\Support\Str::of('taylor@laravel.com'), $user->getAttributes()); // This also works!
$cast->set($user, 'email', null, $user->getAttributes()); // Also valid.
