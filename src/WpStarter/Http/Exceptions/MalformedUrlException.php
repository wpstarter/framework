<?php

namespace WpStarter\Http\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class MalformedUrlException extends HttpException
{
    /**
     * Create a new exception instance.
     */
    public function __construct()
    {
        parent::__construct(400, 'Malformed URL.');
    }
}
