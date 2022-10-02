<?php

namespace WpStarter\Contracts\Support;

interface DeferringDisplayableValue
{
    /**
     * Resolve the displayable value that the class is deferring.
     *
     * @return \WpStarter\Contracts\Support\Htmlable|string
     */
    public function resolveDisplayableValue();
}
