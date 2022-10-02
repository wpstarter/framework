<?php

namespace WpStarter\Container;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    //
}
