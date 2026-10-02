<?php

namespace WpStarter\Events;

use Closure;

if (! function_exists('WpStarter\Events\queueable')) {
    /**
     * Create a new queued Closure event listener.
     *
     * @param  \Closure  $closure
     */
    function queueable(Closure $closure): QueuedClosure
    {
        return new QueuedClosure($closure);
    }
}
