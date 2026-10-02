<?php

namespace WpStarter\Validation\Rules;

use WpStarter\Contracts\Support\Arrayable;
use Stringable;

use function WpStarter\Support\enum_value;

class DoesntContain implements Stringable
{
    /**
     * The values that should be contained in the attribute.
     *
     * @var array
     */
    protected $values;

    /**
     * Create a new doesntContain rule instance.
     *
     * @param  \WpStarter\Contracts\Support\Arrayable|\UnitEnum|array|string  $values
     */
    public function __construct($values)
    {
        if ($values instanceof Arrayable) {
            $values = $values->toArray();
        }

        $this->values = is_array($values) ? $values : func_get_args();
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString()
    {
        $values = array_map(function ($value) {
            $value = enum_value($value);

            return '"'.str_replace('"', '""', $value).'"';
        }, $this->values);

        return 'doesnt_contain:'.implode(',', $values);
    }
}
