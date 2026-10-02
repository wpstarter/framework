<?php

namespace WpStarter\Log;

use Psr\Log\LoggerInterface;

if (! function_exists('WpStarter\Log\log')) {
    /**
     * Log a debug message to the logs.
     *
     * @param  string|null  $message
     * @param  array  $context
     * @return ($message is null ? \Psr\Log\LoggerInterface: null)
     */
    function log($message = null, array $context = []): ?LoggerInterface
    {
        return ws_logger($message, $context);
    }
}
