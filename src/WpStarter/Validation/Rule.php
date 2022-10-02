<?php

namespace WpStarter\Validation;

use WpStarter\Contracts\Support\Arrayable;
use WpStarter\Support\Traits\Macroable;
use WpStarter\Validation\Rules\Dimensions;
use WpStarter\Validation\Rules\Exists;
use WpStarter\Validation\Rules\In;
use WpStarter\Validation\Rules\NotIn;
use WpStarter\Validation\Rules\RequiredIf;
use WpStarter\Validation\Rules\Unique;

class Rule
{
    use Macroable;

    /**
     * Create a new conditional rule set.
     *
     * @param  callable|bool  $condition
     * @param  array|string  $rules
     * @param  array|string  $defaultRules
     * @return \WpStarter\Validation\ConditionalRules
     */
    public static function when($condition, $rules, $defaultRules = [])
    {
        return new ConditionalRules($condition, $rules, $defaultRules);
    }

    /**
     * Get a dimensions constraint builder instance.
     *
     * @param  array  $constraints
     * @return \WpStarter\Validation\Rules\Dimensions
     */
    public static function dimensions(array $constraints = [])
    {
        return new Dimensions($constraints);
    }

    /**
     * Get an exists constraint builder instance.
     *
     * @param  string  $table
     * @param  string  $column
     * @return \WpStarter\Validation\Rules\Exists
     */
    public static function exists($table, $column = 'NULL')
    {
        return new Exists($table, $column);
    }

    /**
     * Get an in constraint builder instance.
     *
     * @param  \WpStarter\Contracts\Support\Arrayable|array|string  $values
     * @return \WpStarter\Validation\Rules\In
     */
    public static function in($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return new In(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a not_in constraint builder instance.
     *
     * @param  \WpStarter\Contracts\Support\Arrayable|array|string  $values
     * @return \WpStarter\Validation\Rules\NotIn
     */
    public static function notIn($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return new NotIn(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a required_if constraint builder instance.
     *
     * @param  callable|bool  $callback
     * @return \WpStarter\Validation\Rules\RequiredIf
     */
    public static function requiredIf($callback)
    {
        return new RequiredIf($callback);
    }

    /**
     * Get a unique constraint builder instance.
     *
     * @param  string  $table
     * @param  string  $column
     * @return \WpStarter\Validation\Rules\Unique
     */
    public static function unique($table, $column = 'NULL')
    {
        return new Unique($table, $column);
    }
}
