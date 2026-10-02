<?php

namespace WpStarter\Database\Query;

use WpStarter\Contracts\Database\Query\Expression as ExpressionContract;
use WpStarter\Database\Grammar;

/**
 * @template TValue of string|int|float
 */
class Expression implements ExpressionContract
{
    /**
     * Create a new raw query expression.
     *
     * @param  TValue  $value
     */
    public function __construct(
        protected $value,
    ) {
    }

    /**
     * Get the value of the expression.
     *
     * @param  \WpStarter\Database\Grammar  $grammar
     * @return TValue
     */
    public function getValue(Grammar $grammar)
    {
        return $this->value;
    }
}
