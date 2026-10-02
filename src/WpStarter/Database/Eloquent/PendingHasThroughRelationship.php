<?php

namespace WpStarter\Database\Eloquent;

use BadMethodCallException;
use WpStarter\Database\Eloquent\Relations\HasMany;
use WpStarter\Database\Eloquent\Relations\MorphOneOrMany;
use WpStarter\Support\Str;
use WpStarter\Support\Stringable;

/**
 * @template TIntermediateModel of \WpStarter\Database\Eloquent\Model
 * @template TDeclaringModel of \WpStarter\Database\Eloquent\Model
 * @template TLocalRelationship of \WpStarter\Database\Eloquent\Relations\HasOneOrMany<TIntermediateModel, TDeclaringModel>
 */
class PendingHasThroughRelationship
{
    /**
     * The root model that the relationship exists on.
     *
     * @var TDeclaringModel
     */
    protected $rootModel;

    /**
     * The local relationship.
     *
     * @var TLocalRelationship
     */
    protected $localRelationship;

    /**
     * Create a pending has-many-through or has-one-through relationship.
     *
     * @param  TDeclaringModel  $rootModel
     * @param  TLocalRelationship  $localRelationship
     */
    public function __construct($rootModel, $localRelationship)
    {
        $this->rootModel = $rootModel;

        $this->localRelationship = $localRelationship;
    }

    /**
     * Define the distant relationship that this model has.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Model
     *
     * @param  string|(callable(TIntermediateModel): (\WpStarter\Database\Eloquent\Relations\HasOne<TRelatedModel, TIntermediateModel>|\WpStarter\Database\Eloquent\Relations\HasMany<TRelatedModel, TIntermediateModel>|\WpStarter\Database\Eloquent\Relations\MorphOneOrMany<TRelatedModel, TIntermediateModel>))  $callback
     * @return (
     *     $callback is string
     *     ? \WpStarter\Database\Eloquent\Relations\HasManyThrough<\WpStarter\Database\Eloquent\Model, TIntermediateModel, TDeclaringModel>|\WpStarter\Database\Eloquent\Relations\HasOneThrough<\WpStarter\Database\Eloquent\Model, TIntermediateModel, TDeclaringModel>
     *     : (
     *         TLocalRelationship is \WpStarter\Database\Eloquent\Relations\HasMany<TIntermediateModel, TDeclaringModel>
     *         ? \WpStarter\Database\Eloquent\Relations\HasManyThrough<TRelatedModel, TIntermediateModel, TDeclaringModel>
     *         : (
     *              $callback is callable(TIntermediateModel): \WpStarter\Database\Eloquent\Relations\HasMany<TRelatedModel, TIntermediateModel>
     *              ? \WpStarter\Database\Eloquent\Relations\HasManyThrough<TRelatedModel, TIntermediateModel, TDeclaringModel>
     *              : \WpStarter\Database\Eloquent\Relations\HasOneThrough<TRelatedModel, TIntermediateModel, TDeclaringModel>
     *         )
     *     )
     * )
     */
    public function has($callback)
    {
        if (is_string($callback)) {
            $callback = fn () => $this->localRelationship->getRelated()->{$callback}();
        }

        $distantRelation = $callback($this->localRelationship->getRelated());

        if ($distantRelation instanceof HasMany || $this->localRelationship instanceof HasMany) {
            $returnedRelation = $this->rootModel->hasManyThrough(
                $distantRelation->getRelated()::class,
                $this->localRelationship->getRelated()::class,
                $this->localRelationship->getForeignKeyName(),
                $distantRelation->getForeignKeyName(),
                $this->localRelationship->getLocalKeyName(),
                $distantRelation->getLocalKeyName(),
            );
        } else {
            $returnedRelation = $this->rootModel->hasOneThrough(
                $distantRelation->getRelated()::class,
                $this->localRelationship->getRelated()::class,
                $this->localRelationship->getForeignKeyName(),
                $distantRelation->getForeignKeyName(),
                $this->localRelationship->getLocalKeyName(),
                $distantRelation->getLocalKeyName(),
            );
        }

        if ($this->localRelationship instanceof MorphOneOrMany) {
            $returnedRelation->where($this->localRelationship->getQualifiedMorphType(), $this->localRelationship->getMorphClass());
        }

        return $returnedRelation;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'has')) {
            return $this->has((new Stringable($method))->after('has')->lcfirst()->toString());
        }

        throw new BadMethodCallException(sprintf(
            'Call to undefined method %s::%s()', static::class, $method
        ));
    }
}
