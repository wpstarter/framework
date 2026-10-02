<?php

namespace WpStarter\Database\Eloquent\Relations\Concerns;

use WpStarter\Database\Eloquent\Contracts\Model as ModelContract;


trait SupportsDefaultModels
{
    /**
     * Indicates if a default model instance should be used.
     *
     * Alternatively, may be a Closure or array.
     *
     * @var \Closure|array|bool
     */
    protected $withDefault;

    /**
     * Make a new related instance for the given model.
     *
     * @param  \WpStarter\Database\Eloquent\Contracts\Model  $parent
     * @return \WpStarter\Database\Eloquent\Contracts\Model
     */
    abstract protected function newRelatedInstanceFor(ModelContract $parent);

    /**
     * Return a new model instance in case the relationship does not exist.
     *
     * @param  \Closure|array|bool  $callback
     * @return $this
     */
    public function withDefault($callback = true)
    {
        $this->withDefault = $callback;

        return $this;
    }

    /**
     * Get the default value for this relation.
     *
     * @param  \WpStarter\Database\Eloquent\Contracts\Model  $parent
     * @return \WpStarter\Database\Eloquent\Contracts\Model|null
     */
    protected function getDefaultFor(ModelContract $parent)
    {
        if (! $this->withDefault) {
            return;
        }

        $instance = $this->newRelatedInstanceFor($parent);

        if (is_callable($this->withDefault)) {
            return call_user_func($this->withDefault, $instance, $parent) ?: $instance;
        }

        if (is_array($this->withDefault)) {
            $instance->forceFill($this->withDefault);
        }

        return $instance;
    }
}
