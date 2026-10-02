<?php

namespace WpStarter\Log\Context\Events;

class ContextHydrated
{
    /**
     * The context instance.
     *
     * @var \WpStarter\Log\Context\Repository
     */
    public $context;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Log\Context\Repository  $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }
}
