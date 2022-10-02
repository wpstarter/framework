<?php

namespace WpStarter\Exceptions;

use Exception;
use Throwable;
use WP_Error;

class WpErrorException extends Exception
{
    protected $wp_error;

    public function __construct(string $message = "",$code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->code=$code;
    }
    public function setWpError(WP_Error $error){
        $this->wp_error=$error;
        return $this;
    }

    /**
     * @return WP_Error|null
     */
    public function getWpError(){
        return $this->wp_error;
    }
}