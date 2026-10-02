<?php

namespace WpStarter\Validation\Rules;

use WpStarter\Contracts\Support\Arrayable;
use Stringable;

use function WpStarter\Support\enum_value;

class ArrayRule implements Stringable
{
    /**
     * The accepted keys.
     *
     * @var array
     */
    protected $keys;

    /**
     * Create a new array rule instance.
     *
     * @param  \WpStarter\Contracts\Support\Arrayable|array|null  $keys
     */
    public function __construct($keys = null)
    {
        if ($keys instanceof Arrayable) {
            $keys = $keys->toArray();
        }

        $this->keys = is_array($keys) ? $keys : func_get_args();
    }

    /**
     * Convert the rule to a validation string.
     *
     * @return string
     */
    public function __toString()
    {
        if (empty($this->keys)) {
            return 'array';
        }

        $keys = array_map(
            static fn ($key) => enum_value($key),
            $this->keys,
        );

        return 'array:'.implode(',', $keys);
    }
}
