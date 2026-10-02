<?php

namespace WpStarter\Contracts\Database\Query;

use WpStarter\Database\Grammar;

interface Expression
{
    /**
     * Get the value of the expression.
     *
     * @param  \WpStarter\Database\Grammar  $grammar
     * @return string|int|float
     */
    public function getValue(Grammar $grammar);
}
