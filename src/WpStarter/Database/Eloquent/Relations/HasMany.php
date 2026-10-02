<?php

namespace WpStarter\Database\Eloquent\Relations;

use WpStarter\Database\Eloquent\Collection as EloquentCollection;

/**
 * @template TRelatedModel of \WpStarter\Database\Eloquent\Model
 * @template TDeclaringModel of \WpStarter\Database\Eloquent\Model
 *
 * @extends \WpStarter\Database\Eloquent\Relations\HasOneOrMany<TRelatedModel, TDeclaringModel, \WpStarter\Database\Eloquent\Collection<int, TRelatedModel>>
 */
class HasMany extends HasOneOrMany
{
    /**
     * Convert the relationship to a "has one" relationship.
     *
     * @return \WpStarter\Database\Eloquent\Relations\HasOne<TRelatedModel, TDeclaringModel>
     */
    public function one()
    {
        return HasOne::noConstraints(fn () => ws_tap(
            new HasOne(
                $this->getQuery(),
                $this->parent,
                $this->foreignKey,
                $this->localKey
            ),
            function ($hasOne) {
                if ($inverse = $this->getInverseRelationship()) {
                    $hasOne->inverse($inverse);
                }
            }
        ));
    }

    /** @inheritDoc */
    public function getResults()
    {
        return ! is_null($this->getParentKey())
            ? $this->query->get()
            : $this->related->newCollection();
    }

    /** @inheritDoc */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /** @inheritDoc */
    public function match(array $models, EloquentCollection $results, $relation)
    {
        return $this->matchMany($models, $results, $relation);
    }
}
