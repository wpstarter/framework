<?php

namespace WpStarter\Validation;

use WpStarter\Contracts\Support\Arrayable;
use WpStarter\Support\Arr;
use WpStarter\Support\Traits\Macroable;
use WpStarter\Validation\Rules\AnyOf;
use WpStarter\Validation\Rules\ArrayRule;
use WpStarter\Validation\Rules\Can;
use WpStarter\Validation\Rules\Date;
use WpStarter\Validation\Rules\Dimensions;
use WpStarter\Validation\Rules\Email;
use WpStarter\Validation\Rules\Enum;
use WpStarter\Validation\Rules\ExcludeIf;
use WpStarter\Validation\Rules\ExcludeUnless;
use WpStarter\Validation\Rules\Exists;
use WpStarter\Validation\Rules\File;
use WpStarter\Validation\Rules\ImageFile;
use WpStarter\Validation\Rules\In;
use WpStarter\Validation\Rules\NotIn;
use WpStarter\Validation\Rules\Numeric;
use WpStarter\Validation\Rules\ProhibitedIf;
use WpStarter\Validation\Rules\ProhibitedUnless;
use WpStarter\Validation\Rules\RequiredIf;
use WpStarter\Validation\Rules\RequiredUnless;
use WpStarter\Validation\Rules\StringRule;
use WpStarter\Validation\Rules\Unique;

class Rule
{
    use Macroable;

    /**
     * Get a can constraint builder instance.
     *
     * @param  string  $ability
     * @param  mixed  ...$arguments
     * @return \WpStarter\Validation\Rules\Can
     */
    public static function can($ability, ...$arguments)
    {
        return new Can($ability, $arguments);
    }

    /**
     * Apply the given rules if the given condition is truthy.
     *
     * @param  callable|bool  $condition
     * @param  \WpStarter\Contracts\Validation\ValidationRule|\WpStarter\Contracts\Validation\InvokableRule|\WpStarter\Contracts\Validation\Rule|\Closure|array|string  $rules
     * @param  \WpStarter\Contracts\Validation\ValidationRule|\WpStarter\Contracts\Validation\InvokableRule|\WpStarter\Contracts\Validation\Rule|\Closure|array|string  $defaultRules
     * @return \WpStarter\Validation\ConditionalRules
     */
    public static function when($condition, $rules, $defaultRules = [])
    {
        return new ConditionalRules($condition, $rules, $defaultRules);
    }

    /**
     * Apply the given rules if the given condition is falsy.
     *
     * @param  callable|bool  $condition
     * @param  \WpStarter\Contracts\Validation\ValidationRule|\WpStarter\Contracts\Validation\InvokableRule|\WpStarter\Contracts\Validation\Rule|\Closure|array|string  $rules
     * @param  \WpStarter\Contracts\Validation\ValidationRule|\WpStarter\Contracts\Validation\InvokableRule|\WpStarter\Contracts\Validation\Rule|\Closure|array|string  $defaultRules
     * @return \WpStarter\Validation\ConditionalRules
     */
    public static function unless($condition, $rules, $defaultRules = [])
    {
        return new ConditionalRules($condition, $defaultRules, $rules);
    }

    /**
     * Get an array rule builder instance.
     *
     * @param  array|null  $keys
     * @return \WpStarter\Validation\Rules\ArrayRule
     */
    public static function array($keys = null)
    {
        return new ArrayRule(...func_get_args());
    }

    /**
     * Create a new nested rule set.
     *
     * @param  callable  $callback
     * @return \WpStarter\Validation\NestedRules
     */
    public static function forEach($callback)
    {
        return new NestedRules($callback);
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
     * Get an in rule builder instance.
     *
     * @param  \WpStarter\Contracts\Support\Arrayable|\UnitEnum|array|string  $values
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
     * Get a not_in rule builder instance.
     *
     * @param  \WpStarter\Contracts\Support\Arrayable|\UnitEnum|array|string  $values
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
     * Get a required_if rule builder instance.
     *
     * @param  (\Closure(): bool)|bool  $callback
     * @return \WpStarter\Validation\Rules\RequiredIf
     */
    public static function requiredIf($callback)
    {
        return new RequiredIf($callback);
    }

    /**
     * Get a required_unless rule builder instance.
     *
     * @param  (\Closure(): bool)|bool  $callback
     * @return \WpStarter\Validation\Rules\RequiredUnless
     */
    public static function requiredUnless($callback)
    {
        return new RequiredUnless($callback);
    }

    /**
     * Get a exclude_if rule builder instance.
     *
     * @param  (\Closure(): bool)|bool  $callback
     * @return \WpStarter\Validation\Rules\ExcludeIf
     */
    public static function excludeIf($callback)
    {
        return new ExcludeIf($callback);
    }

    /**
     * Get a exclude_unless rule builder instance.
     *
     * @param  (\Closure(): bool)|bool  $callback
     * @return \WpStarter\Validation\Rules\ExcludeUnless
     */
    public static function excludeUnless($callback)
    {
        return new ExcludeUnless($callback);
    }

    /**
     * Get a prohibited_if rule builder instance.
     *
     * @param  (\Closure(): bool)|bool  $callback
     * @return \WpStarter\Validation\Rules\ProhibitedIf
     */
    public static function prohibitedIf($callback)
    {
        return new ProhibitedIf($callback);
    }

    /**
     * Get a prohibited_unless rule builder instance.
     *
     * @param  (\Closure(): bool)|bool  $callback
     * @return \WpStarter\Validation\Rules\ProhibitedUnless
     */
    public static function prohibitedUnless($callback)
    {
        return new ProhibitedUnless($callback);
    }

    /**
     * Get a date rule builder instance.
     *
     * @return \WpStarter\Validation\Rules\Date
     */
    public static function date()
    {
        return new Date;
    }

    /**
     * Get a datetime rule builder instance.
     */
    public static function dateTime(): Date
    {
        return (new Date)->format('Y-m-d H:i:s');
    }

    /**
     * Get an email rule builder instance.
     *
     * @return \WpStarter\Validation\Rules\Email
     */
    public static function email()
    {
        return new Email;
    }

    /**
     * Get an enum rule builder instance.
     *
     * @param  class-string  $type
     * @return \WpStarter\Validation\Rules\Enum
     */
    public static function enum($type)
    {
        return new Enum($type);
    }

    /**
     * Get a file rule builder instance.
     *
     * @return \WpStarter\Validation\Rules\File
     */
    public static function file()
    {
        return new File;
    }

    /**
     * Get an image file rule builder instance.
     *
     * @param  bool  $allowSvg
     * @return \WpStarter\Validation\Rules\ImageFile
     */
    public static function imageFile($allowSvg = false)
    {
        return new ImageFile($allowSvg);
    }

    /**
     * Get a dimensions rule builder instance.
     *
     * @param  array  $constraints
     * @return \WpStarter\Validation\Rules\Dimensions
     */
    public static function dimensions(array $constraints = [])
    {
        return new Dimensions($constraints);
    }

    /**
     * Get a string rule builder instance.
     *
     * @return \WpStarter\Validation\Rules\StringRule
     */
    public static function string()
    {
        return new StringRule;
    }

    /**
     * Get a numeric rule builder instance.
     *
     * @return \WpStarter\Validation\Rules\Numeric
     */
    public static function numeric()
    {
        return new Numeric;
    }

    /**
     * Get an "any of" rule builder instance.
     *
     * @param  array  $rules
     * @return \WpStarter\Validation\Rules\AnyOf
     *
     * @throws \InvalidArgumentException
     */
    public static function anyOf($rules)
    {
        return new AnyOf($rules);
    }

    /**
     * Get a contains rule builder instance.
     *
     * @param  \WpStarter\Contracts\Support\Arrayable|\UnitEnum|array|string  $values
     * @return \WpStarter\Validation\Rules\Contains
     */
    public static function contains($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return new Rules\Contains(is_array($values) ? $values : func_get_args());
    }

    /**
     * Get a "does not contain" rule builder instance.
     *
     * @param  \WpStarter\Contracts\Support\Arrayable|\UnitEnum|array|string  $values
     * @return \WpStarter\Validation\Rules\DoesntContain
     */
    public static function doesntContain($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        return new Rules\DoesntContain(is_array($values) ? $values : func_get_args());
    }

    /**
     * Compile a set of rules for an attribute.
     *
     * @param  string  $attribute
     * @param  array  $rules
     * @param  array|null  $data
     * @return object|\stdClass
     */
    public static function compile($attribute, $rules, $data = null)
    {
        $parser = new ValidationRuleParser(
            Arr::undot(Arr::wrap($data))
        );

        if (is_array($rules) && ! array_is_list($rules)) {
            $nested = [];

            foreach ($rules as $key => $rule) {
                $nested[$attribute.'.'.$key] = $rule;
            }

            $rules = $nested;
        } else {
            $rules = [$attribute => $rules];
        }

        return $parser->explode(ValidationRuleParser::filterConditionalRules($rules, $data));
    }
}
