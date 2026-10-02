<?php

namespace WpStarter\Foundation\Exceptions\Whoops;

use WpStarter\Contracts\Foundation\ExceptionRenderer;
use Whoops\Run as Whoops;

use function tap;

class WhoopsExceptionRenderer implements ExceptionRenderer
{
    /**
     * Renders the given exception as HTML.
     *
     * @param  \Throwable  $throwable
     * @return string
     */
    public function render($throwable)
    {
        return tap(new Whoops, function ($whoops) {
            $whoops->appendHandler($this->whoopsHandler());

            $whoops->writeToOutput(false);

            $whoops->allowQuit(false);
        })->handleException($throwable);
    }

    /**
     * Get the Whoops handler for the application.
     *
     * @return \Whoops\Handler\Handler
     */
    protected function whoopsHandler()
    {
        return (new WhoopsHandler)->forDebug();
    }
}
