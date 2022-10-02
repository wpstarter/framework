<?php

namespace WpStarter\Database\Eloquent\Factories;

use WpStarter\Database\Eloquent\Model;
use WpStarter\Support\Collection;

class BelongsToManyRelationship
{
    /**
     * The related factory instance.
     *
     * @var \WpStarter\Database\Eloquent\Factories\Factory|\WpStarter\Support\Collection|\WpStarter\Database\Eloquent\Model
     */
    protected $factory;

    /**
     * The pivot attributes / attribute resolver.
     *
     * @var callable|array
     */
    protected $pivot;

    /**
     * The relationship name.
     *
     * @var string
     */
    protected $relationship;

    /**
     * Create a new attached relationship definition.
     *
     * @param  \WpStarter\Database\Eloquent\Factories\Factory|\WpStarter\Support\Collection|\WpStarter\Database\Eloquent\Model  $factory
     * @param  callable|array  $pivot
     * @param  string  $relationship
     * @return void
     */
    public function __construct($factory, $pivot, $relationship)
    {
        $this->factory = $factory;
        $this->pivot = $pivot;
        $this->relationship = $relationship;
    }

    /**
     * Create the attached relationship for the given model.
     *
     * @param  \WpStarter\Database\Eloquent\Model  $model
     * @return void
     */
    public function createFor(Model $model)
    {
        Collection::wrap($this->factory instanceof Factory ? $this->factory->create([], $model) : $this->factory)->each(function ($attachable) use ($model) {
            $model->{$this->relationship}()->attach(
                $attachable,
                is_callable($this->pivot) ? call_user_func($this->pivot, $model) : $this->pivot
            );
        });
    }
}
