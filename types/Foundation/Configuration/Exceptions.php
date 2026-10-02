<?php

use WpStarter\Container\Container;
use WpStarter\Database\Eloquent\ModelNotFoundException;
use WpStarter\Foundation\Configuration\Exceptions;
use WpStarter\Foundation\Exceptions\Handler;
use Symfony\Component\HttpKernel\Exception\HttpException;

$exceptions = new Exceptions(
    new Handler(
        new Container,
    ),
);

$exceptions->stopIgnoring(HttpException::class);
$exceptions->stopIgnoring([ModelNotFoundException::class]);
