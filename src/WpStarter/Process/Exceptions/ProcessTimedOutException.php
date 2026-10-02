<?php

namespace WpStarter\Process\Exceptions;

use WpStarter\Contracts\Process\ProcessResult;
use Symfony\Component\Process\Exception\ProcessTimedOutException as SymfonyTimeoutException;
use Symfony\Component\Process\Exception\RuntimeException;

class ProcessTimedOutException extends RuntimeException
{
    /**
     * The process result instance.
     *
     * @var \WpStarter\Contracts\Process\ProcessResult
     */
    public $result;

    /**
     * Create a new exception instance.
     *
     * @param  \Symfony\Component\Process\Exception\ProcessTimedOutException  $original
     * @param  \WpStarter\Contracts\Process\ProcessResult  $result
     */
    public function __construct(SymfonyTimeoutException $original, ProcessResult $result)
    {
        $this->result = $result;

        parent::__construct($original->getMessage(), $original->getCode(), $original);
    }
}
