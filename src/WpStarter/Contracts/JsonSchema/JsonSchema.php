<?php

namespace WpStarter\Contracts\JsonSchema;

use Closure;

interface JsonSchema
{
    /**
     * Create a new object schema instance.
     *
     * @param  (Closure(JsonSchema): array<string, \WpStarter\JsonSchema\Types\Type>)|array<string, \WpStarter\JsonSchema\Types\Type>  $properties
     * @return \WpStarter\JsonSchema\Types\ObjectType
     */
    public function object(Closure|array $properties = []);

    /**
     * Create a new array property instance.
     *
     * @return \WpStarter\JsonSchema\Types\ArrayType
     */
    public function array();

    /**
     * Create a new string property instance.
     *
     * @return \WpStarter\JsonSchema\Types\StringType
     */
    public function string();

    /**
     * Create a new integer property instance.
     *
     * @return \WpStarter\JsonSchema\Types\IntegerType
     */
    public function integer();

    /**
     * Create a new number property instance.
     *
     * @return \WpStarter\JsonSchema\Types\NumberType
     */
    public function number();

    /**
     * Create a new boolean property instance.
     *
     * @return \WpStarter\JsonSchema\Types\BooleanType
     */
    public function boolean();

    /**
     * Create a new multi-type union instance.
     *
     * @param  array<int, string>  $types
     * @return \WpStarter\JsonSchema\Types\UnionType
     */
    public function union(array $types);
}
