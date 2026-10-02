<?php

namespace WpStarter\Database\Eloquent\Contracts;

use ArrayAccess;
use JsonSerializable;
use Stringable;
use WpStarter\Contracts\Broadcasting\HasBroadcastChannel;
use WpStarter\Contracts\Queue\QueueableEntity;
use WpStarter\Contracts\Routing\UrlRoutable;
use WpStarter\Contracts\Support\Arrayable;
use WpStarter\Contracts\Support\CanBeEscapedWhenCastToString;
use WpStarter\Contracts\Support\Jsonable;

/**
 * The public model API shared by Eloquent models and independent adapters.
 *
 * Implementations must also expose the public state used by Eloquent builders.
 * A WordPress adapter must implement this API; a native WP_User alone does not.
 *
 * @property bool $exists
 * @property bool $wasRecentlyCreated
 * @property bool $incrementing
 * @property bool $preventsLazyLoading
 * @property bool $timestamps
 */
interface Model extends Arrayable, ArrayAccess, CanBeEscapedWhenCastToString, HasBroadcastChannel, Jsonable, JsonSerializable, QueueableEntity, Stringable, UrlRoutable
{
    /**
     * Clear the list of booted models so they will be re-booted.
     *
     * @return void
     */
    public static function clearBootedModels();

    /**
     * Disables relationship model touching for the current class during given callback scope.
     *
     * @param  callable  $callback
     * @return void
     */
    public static function withoutTouching(callable $callback);

    /**
     * Disables relationship model touching for the given model classes during given callback scope.
     *
     * @param  array  $models
     * @param  callable  $callback
     * @return void
     */
    public static function withoutTouchingOn(array $models, callable $callback);

    /**
     * Determine if the given model is ignoring touches.
     *
     * @param  string|null  $class
     * @return bool
     */
    public static function isIgnoringTouch($class = null);

    /**
     * Indicate that models should prevent lazy loading, silently discarding attributes, and accessing missing attributes.
     *
     * @param  bool  $shouldBeStrict
     * @return void
     */
    public static function shouldBeStrict(bool $shouldBeStrict = true);

    /**
     * Prevent model relationships from being lazy loaded.
     *
     * @param  bool  $value
     * @return void
     */
    public static function preventLazyLoading($value = true);

    /**
     * Determine if model relationships should be automatically eager loaded when accessed.
     *
     * @param  bool  $value
     * @return void
     */
    public static function automaticallyEagerLoadRelationships($value = true);

    /**
     * Register a callback that is responsible for handling lazy loading violations.
     *
     * @param  (callable(self, string))|null  $callback
     * @return void
     */
    public static function handleLazyLoadingViolationUsing(?callable $callback);

    /**
     * Prevent non-fillable attributes from being silently discarded.
     *
     * @param  bool  $value
     * @return void
     */
    public static function preventSilentlyDiscardingAttributes($value = true);

    /**
     * Register a callback that is responsible for handling discarded attribute violations.
     *
     * @param  (callable(self, array))|null  $callback
     * @return void
     */
    public static function handleDiscardedAttributeViolationUsing(?callable $callback);

    /**
     * Prevent accessing missing attributes on retrieved models.
     *
     * @param  bool  $value
     * @return void
     */
    public static function preventAccessingMissingAttributes($value = true);

    /**
     * Register a callback that is responsible for handling missing attribute violations.
     *
     * @param  (callable(self, string))|null  $callback
     * @return void
     */
    public static function handleMissingAttributeViolationUsing(?callable $callback);

    /**
     * Execute a callback without broadcasting any model events for all model types.
     *
     * @param  callable  $callback
     * @return mixed
     */
    public static function withoutBroadcasting(callable $callback);

    /**
     * Fill the model with an array of attributes.
     *
     * @param  array<string, mixed>  $attributes
     * @return $this
     *
     * @throws \WpStarter\Database\Eloquent\MassAssignmentException
     */
    public function fill(array $attributes);

    /**
     * Fill the model with an array of attributes. Force mass assignment.
     *
     * @param  array<string, mixed>  $attributes
     * @return $this
     */
    public function forceFill(array $attributes);

    /**
     * Qualify the given column name by the model's table.
     *
     * @param  string  $column
     * @return string
     */
    public function qualifyColumn($column);

    /**
     * Qualify the given columns with the model's table.
     *
     * @param  array  $columns
     * @return array
     */
    public function qualifyColumns($columns);

    /**
     * Create a new instance of the given model.
     *
     * @param  array<string, mixed>  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false);

    /**
     * Create a new model instance that is existing.
     *
     * @param  array<string, mixed>  $attributes
     * @param  \UnitEnum|string|null  $connection
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null);

    /**
     * Begin querying the model on a given connection.
     *
     * @param  \UnitEnum|string|null  $connection
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public static function on($connection = null);

    /**
     * Begin querying the model on the write connection.
     *
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public static function onWriteConnection();

    /**
     * Get all of the models from the database.
     *
     * @param  array|string  $columns
     * @return \WpStarter\Database\Eloquent\Collection<int, static>
     */
    public static function all($columns = ['*']);

    /**
     * Begin querying a model with eager loading.
     *
     * @param  array|string  $relations
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public static function with($relations);

    /**
     * Eager load relations on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function load($relations);

    /**
     * Eager load relationships on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @return $this
     */
    public function loadMorph($relation, $relations);

    /**
     * Eager load relations on the model if they are not already eager loaded.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadMissing($relations);

    /**
     * Eager load relation's column aggregations on the model.
     *
     * @param  array|string  $relations
     * @param  string  $column
     * @param  string|null  $function
     * @return $this
     */
    public function loadAggregate($relations, $column, $function = null);

    /**
     * Eager load relation counts on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadCount($relations);

    /**
     * Eager load relation max column values on the model.
     *
     * @param  array|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMax($relations, $column);

    /**
     * Eager load relation min column values on the model.
     *
     * @param  array|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMin($relations, $column);

    /**
     * Eager load relation's column summations on the model.
     *
     * @param  array|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadSum($relations, $column);

    /**
     * Eager load relation average column values on the model.
     *
     * @param  array|string  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadAvg($relations, $column);

    /**
     * Eager load related model existence values on the model.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function loadExists($relations);

    /**
     * Eager load relationship column aggregation on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @param  string  $column
     * @param  string|null  $function
     * @return $this
     */
    public function loadMorphAggregate($relation, $relations, $column, $function = null);

    /**
     * Eager load relationship counts on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @return $this
     */
    public function loadMorphCount($relation, $relations);

    /**
     * Eager load relationship max column values on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMorphMax($relation, $relations, $column);

    /**
     * Eager load relationship min column values on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMorphMin($relation, $relations, $column);

    /**
     * Eager load relationship column summations on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMorphSum($relation, $relations, $column);

    /**
     * Eager load relationship average column values on the polymorphic relation of a model.
     *
     * @param  string  $relation
     * @param  array  $relations
     * @param  string  $column
     * @return $this
     */
    public function loadMorphAvg($relation, $relations, $column);

    /**
     * Update the model in the database.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = []);

    /**
     * Update the model in the database within a transaction.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $options
     * @return bool
     *
     * @throws \Throwable
     */
    public function updateOrFail(array $attributes = [], array $options = []);

    /**
     * Update the model in the database without raising any events.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, mixed>  $options
     * @return bool
     */
    public function updateQuietly(array $attributes = [], array $options = []);

    /**
     * Save the model and all of its relationships.
     *
     * @return bool
     */
    public function push();

    /**
     * Save the model and all of its relationships without raising any events to the parent model.
     *
     * @return bool
     */
    public function pushQuietly();

    /**
     * Save the model to the database without raising any events.
     *
     * @param  array  $options
     * @return bool
     */
    public function saveQuietly(array $options = []);

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = []);

    /**
     * Save the model to the database within a transaction.
     *
     * @param  array  $options
     * @return bool
     *
     * @throws \Throwable
     */
    public function saveOrFail(array $options = []);

    /**
     * Destroy the models for the given IDs.
     *
     * @param  \WpStarter\Support\Collection|array|int|string  $ids
     * @return int
     */
    public static function destroy($ids);

    /**
     * Delete the model from the database.
     *
     * @return bool|null
     *
     * @throws \LogicException
     */
    public function delete();

    /**
     * Delete the model from the database without raising any events.
     *
     * @return bool
     */
    public function deleteQuietly();

    /**
     * Delete the model from the database within a transaction.
     *
     * @return bool|null
     *
     * @throws \Throwable
     */
    public function deleteOrFail();

    /**
     * Force a hard delete on a soft deleted model.
     *
     * This method protects developers from running forceDelete when the trait is missing.
     *
     * @return bool|null
     */
    public function forceDelete();

    /**
     * Force a hard destroy on a soft deleted model.
     *
     * This method protects developers from running forceDestroy when the trait is missing.
     *
     * @param  \WpStarter\Support\Collection|array|int|string  $ids
     * @return bool|null
     */
    public static function forceDestroy($ids);

    /**
     * Begin querying the model.
     *
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public static function query();

    /**
     * Get a new query builder for the model's table.
     *
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public function newQuery();

    /**
     * Get a new query builder that doesn't have any global scopes or eager loading.
     *
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public function newModelQuery();

    /**
     * Get a new query builder with no relationships loaded.
     *
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public function newQueryWithoutRelationships();

    /**
     * Register the global scopes for this builder instance.
     *
     * @param  \WpStarter\Database\Eloquent\Builder<static>  $builder
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public function registerGlobalScopes($builder);

    /**
     * Get a new query builder that doesn't have any global scopes.
     *
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public function newQueryWithoutScopes();

    /**
     * Get a new query instance without a given scope.
     *
     * @param  \WpStarter\Database\Eloquent\Scope|string  $scope
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public function newQueryWithoutScope($scope);

    /**
     * Get a new query to restore one or more models by their queueable IDs.
     *
     * @param  array|int  $ids
     * @return \WpStarter\Database\Eloquent\Builder<static>
     */
    public function newQueryForRestoration($ids);

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \WpStarter\Database\Query\Builder  $query
     * @return \WpStarter\Database\Eloquent\Builder<*>
     */
    public function newEloquentBuilder($query);

    /**
     * Create a new pivot model instance.
     *
     * @param  \WpStarter\Database\Eloquent\Contracts\Model  $parent
     * @param  array<string, mixed>  $attributes
     * @param  string  $table
     * @param  bool  $exists
     * @param  string|null  $using
     * @return \WpStarter\Database\Eloquent\Relations\Pivot
     */
    public function newPivot(\WpStarter\Database\Eloquent\Contracts\Model $parent, array $attributes, $table, $exists, $using = null);

    /**
     * Determine if the model has a given scope.
     *
     * @param  string  $scope
     * @return bool
     */
    public function hasNamedScope($scope);

    /**
     * Apply the given named scope if possible.
     *
     * @param  string  $scope
     * @param  array  $parameters
     * @return mixed
     */
    public function callNamedScope($scope, array $parameters = []);

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray();

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws \WpStarter\Database\Eloquent\JsonEncodingException
     */
    public function toJson($options = 0);

    /**
     * Convert the model instance to pretty print formatted JSON.
     *
     * @param  int  $options
     * @return string
     *
     * @throws \WpStarter\Database\Eloquent\JsonEncodingException
     */
    public function toPrettyJson(int $options = 0);

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed;

    /**
     * Reload a fresh model instance from the database.
     *
     * @param  array|string  $with
     * @return static|null
     */
    public function fresh($with = []);

    /**
     * Reload the current model instance with fresh attributes from the database.
     *
     * @return $this
     */
    public function refresh();

    /**
     * Clone the model into a new, non-existing instance.
     *
     * @param  array|null  $except
     * @return static
     */
    public function replicate(?array $except = null);

    /**
     * Clone the model into a new, non-existing instance without raising any events.
     *
     * @param  array|null  $except
     * @return static
     */
    public function replicateQuietly(?array $except = null);

    /**
     * Determine if two models have the same ID and belong to the same table.
     *
     * @param  \WpStarter\Database\Eloquent\Contracts\Model|null  $model
     * @return bool
     */
    public function is($model);

    /**
     * Determine if two models are not the same.
     *
     * @param  \WpStarter\Database\Eloquent\Contracts\Model|null  $model
     * @return bool
     */
    public function isNot($model);

    /**
     * Get the database connection for the model.
     *
     * @return \WpStarter\Database\Connection
     */
    public function getConnection();

    /**
     * Get the current connection name for the model.
     *
     * @return string|null
     */
    public function getConnectionName();

    /**
     * Set the connection associated with the model.
     *
     * @param  \UnitEnum|string|null  $name
     * @return $this
     */
    public function setConnection($name);

    /**
     * Resolve a connection instance.
     *
     * @param  \UnitEnum|string|null  $connection
     * @return \WpStarter\Database\Connection
     */
    public static function resolveConnection($connection = null);

    /**
     * Get the connection resolver instance.
     *
     * @return \WpStarter\Database\ConnectionResolverInterface|null
     */
    public static function getConnectionResolver();

    /**
     * Set the connection resolver instance.
     *
     * @param  \WpStarter\Database\ConnectionResolverInterface  $resolver
     * @return void
     */
    public static function setConnectionResolver(\WpStarter\Database\ConnectionResolverInterface $resolver);

    /**
     * Unset the connection resolver for models.
     *
     * @return void
     */
    public static function unsetConnectionResolver();

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable();

    /**
     * Set the table associated with the model.
     *
     * @param  string  $table
     * @return $this
     */
    public function setTable($table);

    /**
     * Get the primary key for the model.
     *
     * @return string
     */
    public function getKeyName();

    /**
     * Set the primary key for the model.
     *
     * @param  string  $key
     * @return $this
     */
    public function setKeyName($key);

    /**
     * Get the table qualified key name.
     *
     * @return string
     */
    public function getQualifiedKeyName();

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType();

    /**
     * Set the data type for the primary key.
     *
     * @param  string  $type
     * @return $this
     */
    public function setKeyType($type);

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing();

    /**
     * Set whether IDs are incrementing.
     *
     * @param  bool  $value
     * @return $this
     */
    public function setIncrementing($value);

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey();

    /**
     * Get the queueable identity for the entity.
     *
     * @return mixed
     */
    public function getQueueableId();

    /**
     * Get the queueable relationships for the entity.
     *
     * @return array
     */
    public function getQueueableRelations();

    /**
     * Get the queueable connection for the entity.
     *
     * @return string|null
     */
    public function getQueueableConnection();

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey();

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName();

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \WpStarter\Database\Eloquent\Contracts\Model|null
     */
    public function resolveRouteBinding($value, $field = null);

    /**
     * Retrieve the model for a bound value.
     *
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \WpStarter\Database\Eloquent\Contracts\Model|null
     */
    public function resolveSoftDeletableRouteBinding($value, $field = null);

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \WpStarter\Database\Eloquent\Contracts\Model|null
     */
    public function resolveChildRouteBinding($childType, $value, $field);

    /**
     * Retrieve the child model for a bound value.
     *
     * @param  string  $childType
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \WpStarter\Database\Eloquent\Contracts\Model|null
     */
    public function resolveSoftDeletableChildRouteBinding($childType, $value, $field);

    /**
     * Retrieve the model for a bound value.
     *
     * @param  \WpStarter\Database\Eloquent\Contracts\Model|\WpStarter\Contracts\Database\Eloquent\Builder|\WpStarter\Database\Eloquent\Relations\Relation  $query
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \WpStarter\Contracts\Database\Eloquent\Builder
     */
    public function resolveRouteBindingQuery($query, $value, $field = null);

    /**
     * Get the default foreign key name for the model.
     *
     * @return string
     */
    public function getForeignKey();

    /**
     * Get the number of models to return per page.
     *
     * @return int
     */
    public function getPerPage();

    /**
     * Set the number of models to return per page.
     *
     * @param  int  $perPage
     * @return $this
     */
    public function setPerPage($perPage);

    /**
     * Determine if the model is soft deletable.
     */
    public static function isSoftDeletable(): bool;

    /**
     * Determine if lazy loading is disabled.
     *
     * @return bool
     */
    public static function preventsLazyLoading();

    /**
     * Determine if relationships are being automatically eager loaded when accessed.
     *
     * @return bool
     */
    public static function isAutomaticallyEagerLoadingRelationships();

    /**
     * Determine if discarding guarded attribute fills is disabled.
     *
     * @return bool
     */
    public static function preventsSilentlyDiscardingAttributes();

    /**
     * Determine if accessing missing attributes is disabled.
     *
     * @return bool
     */
    public static function preventsAccessingMissingAttributes();

    /**
     * Get the broadcast channel route definition that is associated with the given entity.
     *
     * @return string
     */
    public function broadcastChannelRoute();

    /**
     * Get the broadcast channel name that is associated with the given entity.
     *
     * @return string
     */
    public function broadcastChannel();

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key);

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value);

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset): bool;

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed;

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value): void;

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset): void;

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key);

    /**
     * Unset an attribute on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key);

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters);

    /**
     * Handle dynamic static method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters);

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString(): string;

    /**
     * Indicate that the object's string representation should be escaped when __toString is invoked.
     *
     * @param  bool  $escape
     * @return $this
     */
    public function escapeWhenCastingToString($escape = true);

    /**
     * Convert the model's attributes to an array.
     *
     * @return array<string, mixed>
     */
    public function attributesToArray();

    /**
     * Get the model's relationships in array form.
     *
     * @return array
     */
    public function relationsToArray();

    /**
     * Determine whether an attribute exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasAttribute($key);

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key);

    /**
     * Get a plain attribute (not a relationship).
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttributeValue($key);

    /**
     * Get a relationship.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getRelationValue($key);

    /**
     * Determine if the given key is a relationship method on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function isRelation($key);

    /**
     * Determine if a get mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasGetMutator($key);

    /**
     * Determine if a "\WpStarter\Database\Eloquent\Casts\Attribute" return type marked mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasAttributeMutator($key);

    /**
     * Determine if a "\WpStarter\Database\Eloquent\Casts\Attribute" return type marked get mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasAttributeGetMutator($key);

    /**
     * Determine if any get mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasAnyGetMutator($key);

    /**
     * Merge new casts with existing casts on the model.
     *
     * @param  array  $casts
     * @return $this
     */
    public function mergeCasts($casts);

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value);

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasSetMutator($key);

    /**
     * Determine if an "\WpStarter\Database\Eloquent\Casts\Attribute" return type marked set mutator exists for an attribute.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasAttributeSetMutator($key);

    /**
     * Set a given JSON attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function fillJsonAttribute($key, $value);

    /**
     * Decode the given JSON back into an array or object.
     *
     * @param  string|null  $value
     * @param  bool  $asObject
     * @return mixed
     */
    public function fromJson($value, $asObject = false);

    /**
     * Decrypt the given encrypted string.
     *
     * @param  string  $value
     * @return mixed
     */
    public function fromEncryptedString($value);

    /**
     * Set the encrypter instance that will be used to encrypt attributes.
     *
     * @param  \WpStarter\Contracts\Encryption\Encrypter|null  $encrypter
     * @return void
     */
    public static function encryptUsing($encrypter);

    /**
     * Get the current encrypter being used by the model.
     *
     * @return \WpStarter\Contracts\Encryption\Encrypter
     */
    public static function currentEncrypter();

    /**
     * Decode the given float.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function fromFloat($value);

    /**
     * Convert a DateTime to a storable string.
     *
     * @param  mixed  $value
     * @return string|null
     */
    public function fromDateTime($value);

    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array<int, string|null>
     */
    public function getDates();

    /**
     * Get the format for database stored dates.
     *
     * @return string
     */
    public function getDateFormat();

    /**
     * Set the date format used by the model.
     *
     * @param  string  $format
     * @return $this
     */
    public function setDateFormat($format);

    /**
     * Determine whether an attribute should be cast to a native type.
     *
     * @param  string  $key
     * @param  array|string|null  $types
     * @return bool
     */
    public function hasCast($key, $types = null);

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    public function getCasts();

    /**
     * Get all of the current attributes on the model.
     *
     * @return array<string, mixed>
     */
    public function getAttributes();

    /**
     * Set the array of model attributes. No checking is done.
     *
     * @param  array  $attributes
     * @param  bool  $sync
     * @return $this
     */
    public function setRawAttributes(array $attributes, $sync = false);

    /**
     * Get the model's original attribute values.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? array<string, mixed> : mixed)
     */
    public function getOriginal($key = null, $default = null);

    /**
     * Get the model's raw original attribute values.
     *
     * @param  string|null  $key
     * @param  mixed  $default
     * @return ($key is null ? array<string, mixed> : mixed)
     */
    public function getRawOriginal($key = null, $default = null);

    /**
     * Get a subset of the model's attributes.
     *
     * @param  array<string>|mixed  $attributes
     * @return array<string, mixed>
     */
    public function only($attributes);

    /**
     * Get all attributes except the given ones.
     *
     * @param  array<string>|mixed  $attributes
     * @return array
     */
    public function except($attributes);

    /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncOriginal();

    /**
     * Sync a single original attribute with its current value.
     *
     * @param  string  $attribute
     * @return $this
     */
    public function syncOriginalAttribute($attribute);

    /**
     * Sync multiple original attribute with their current values.
     *
     * @param  array<string>|string  $attributes
     * @return $this
     */
    public function syncOriginalAttributes($attributes);

    /**
     * Sync the changed attributes.
     *
     * @return $this
     */
    public function syncChanges();

    /**
     * Determine if the model or any of the given attribute(s) have been modified.
     *
     * @param  array<string>|string|null  $attributes
     * @return bool
     */
    public function isDirty($attributes = null);

    /**
     * Determine if the model or all the given attribute(s) have remained the same.
     *
     * @param  array<string>|string|null  $attributes
     * @return bool
     */
    public function isClean($attributes = null);

    /**
     * Discard attribute changes and reset the attributes to their original state.
     *
     * @return $this
     */
    public function discardChanges();

    /**
     * Determine if the model or any of the given attribute(s) were changed when the model was last saved.
     *
     * @param  array<string>|string|null  $attributes
     * @return bool
     */
    public function wasChanged($attributes = null);

    /**
     * Get the attributes that have been changed since the last sync.
     *
     * @return array<string, mixed>
     */
    public function getDirty();

    /**
     * Get the attributes that were changed when the model was last saved.
     *
     * @return array<string, mixed>
     */
    public function getChanges();

    /**
     * Get the attributes that were previously original before the model was last saved.
     *
     * @return array<string, mixed>
     */
    public function getPrevious();

    /**
     * Determine if the new and old values for a given key are equivalent.
     *
     * @param  string  $key
     * @return bool
     */
    public function originalIsEquivalent($key);

    /**
     * Append attributes to query when building a query.
     *
     * @param  array<string>|string  $attributes
     * @return $this
     */
    public function append($attributes);

    /**
     * Get the accessors that are being appended to model arrays.
     *
     * @return array
     */
    public function getAppends();

    /**
     * Set the accessors to append to model arrays.
     *
     * @param  array  $appends
     * @return $this
     */
    public function setAppends(array $appends);

    /**
     * Merge new appended attributes with existing appended attributes on the model.
     *
     * @param  array<string>  $appends
     * @return $this
     */
    public function mergeAppends(array $appends);

    /**
     * Return whether the accessor attribute has been appended.
     *
     * @param  string  $attribute
     * @return bool
     */
    public function hasAppended($attribute);

    /**
     * Remove all appended properties from the model.
     *
     * @return $this
     */
    public function withoutAppends();

    /**
     * Get the mutated attributes for a given instance.
     *
     * @return array
     */
    public function getMutatedAttributes();

    /**
     * Extract and cache all the mutated attributes of a class.
     *
     * @param  object|string  $classOrInstance
     * @return void
     */
    public static function cacheMutatedAttributes($classOrInstance);

    /**
     * Boot the has event trait for a model.
     *
     * @return void
     */
    public static function bootHasEvents();

    /**
     * Resolve the observe class names from the attributes.
     *
     * @return array
     */
    public static function resolveObserveAttributes();

    /**
     * Register observers with the model.
     *
     * @param  object|string[]|string  $classes
     * @return void
     *
     * @throws \RuntimeException
     */
    public static function observe($classes);

    /**
     * Get the observable event names.
     *
     * @return string[]
     */
    public function getObservableEvents();

    /**
     * Set the observable event names.
     *
     * @param  string[]  $observables
     * @return $this
     */
    public function setObservableEvents(array $observables);

    /**
     * Add an observable event name.
     *
     * @param  string|string[]  $observables
     * @return void
     */
    public function addObservableEvents($observables);

    /**
     * Remove an observable event name.
     *
     * @param  string|string[]  $observables
     * @return void
     */
    public function removeObservableEvents($observables);

    /**
     * Register a retrieved model event with the dispatcher.
     *
     * @param  \WpStarter\Events\QueuedClosure|callable|array|class-string  $callback
     * @return void
     */
    public static function retrieved($callback);

    /**
     * Register a saving model event with the dispatcher.
     *
     * @param  \WpStarter\Events\QueuedClosure|callable|array|class-string  $callback
     * @return void
     */
    public static function saving($callback);

    /**
     * Register a saved model event with the dispatcher.
     *
     * @param  \WpStarter\Events\QueuedClosure|callable|array|class-string  $callback
     * @return void
     */
    public static function saved($callback);

    /**
     * Register an updating model event with the dispatcher.
     *
     * @param  \WpStarter\Events\QueuedClosure|callable|array|class-string  $callback
     * @return void
     */
    public static function updating($callback);

    /**
     * Register an updated model event with the dispatcher.
     *
     * @param  \WpStarter\Events\QueuedClosure|callable|array|class-string  $callback
     * @return void
     */
    public static function updated($callback);

    /**
     * Register a creating model event with the dispatcher.
     *
     * @param  \WpStarter\Events\QueuedClosure|callable|array|class-string  $callback
     * @return void
     */
    public static function creating($callback);

    /**
     * Register a created model event with the dispatcher.
     *
     * @param  \WpStarter\Events\QueuedClosure|callable|array|class-string  $callback
     * @return void
     */
    public static function created($callback);

    /**
     * Register a replicating model event with the dispatcher.
     *
     * @param  \WpStarter\Events\QueuedClosure|callable|array|class-string  $callback
     * @return void
     */
    public static function replicating($callback);

    /**
     * Register a deleting model event with the dispatcher.
     *
     * @param  \WpStarter\Events\QueuedClosure|callable|array|class-string  $callback
     * @return void
     */
    public static function deleting($callback);

    /**
     * Register a deleted model event with the dispatcher.
     *
     * @param  \WpStarter\Events\QueuedClosure|callable|array|class-string  $callback
     * @return void
     */
    public static function deleted($callback);

    /**
     * Remove all the event listeners for the model.
     *
     * @return void
     */
    public static function flushEventListeners();

    /**
     * Get the event map for the model.
     *
     * @return array
     */
    public function dispatchesEvents();

    /**
     * Get the event dispatcher instance.
     *
     * @return \WpStarter\Contracts\Events\Dispatcher|null
     */
    public static function getEventDispatcher();

    /**
     * Set the event dispatcher instance.
     *
     * @param  \WpStarter\Contracts\Events\Dispatcher  $dispatcher
     * @return void
     */
    public static function setEventDispatcher(\WpStarter\Contracts\Events\Dispatcher $dispatcher);

    /**
     * Unset the event dispatcher for models.
     *
     * @return void
     */
    public static function unsetEventDispatcher();

    /**
     * Execute a callback without firing any model events for any model type.
     *
     * @param  callable  $callback
     * @return mixed
     */
    public static function withoutEvents(callable $callback);

    /**
     * Boot the has global scopes trait for a model.
     *
     * @return void
     */
    public static function bootHasGlobalScopes();

    /**
     * Resolve the global scope class names from the attributes.
     *
     * @return array
     */
    public static function resolveGlobalScopeAttributes();

    /**
     * Register a new global scope on the model.
     *
     * @param  \WpStarter\Database\Eloquent\Scope|(\Closure(\WpStarter\Database\Eloquent\Builder<static>): mixed)|string  $scope
     * @param  \WpStarter\Database\Eloquent\Scope|(\Closure(\WpStarter\Database\Eloquent\Builder<static>): mixed)|null  $implementation
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function addGlobalScope($scope, $implementation = null);

    /**
     * Register multiple global scopes on the model.
     *
     * @param  array  $scopes
     * @return void
     */
    public static function addGlobalScopes(array $scopes);

    /**
     * Determine if a model has a global scope.
     *
     * @param  \WpStarter\Database\Eloquent\Scope|string  $scope
     * @return bool
     */
    public static function hasGlobalScope($scope);

    /**
     * Get a global scope registered with the model.
     *
     * @param  \WpStarter\Database\Eloquent\Scope|string  $scope
     * @return \WpStarter\Database\Eloquent\Scope|(\Closure(\WpStarter\Database\Eloquent\Builder<static>): mixed)|null
     */
    public static function getGlobalScope($scope);

    /**
     * Get all of the global scopes that are currently registered.
     *
     * @return array
     */
    public static function getAllGlobalScopes();

    /**
     * Set the current global scopes.
     *
     * @param  array  $scopes
     * @return void
     */
    public static function setAllGlobalScopes($scopes);

    /**
     * Get the global scopes for this class instance.
     *
     * @return array
     */
    public function getGlobalScopes();

    /**
     * Get the dynamic relation resolver if defined or inherited, or return null.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $class
     * @param  string  $key
     * @return \Closure|null
     */
    public function relationResolver($class, $key);

    /**
     * Define a dynamic relation resolver.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    public static function resolveRelationUsing($name, \Closure $callback);

    /**
     * Determine if a relationship autoloader callback has been defined.
     *
     * @return bool
     */
    public function hasRelationAutoloadCallback();

    /**
     * Define an automatic relationship autoloader callback for this model and its relations.
     *
     * @param  \Closure  $callback
     * @param  mixed  $context
     * @return $this
     */
    public function autoloadRelationsUsing(\Closure $callback, $context = null);

    /**
     * Define a one-to-one relationship.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $localKey
     * @return \WpStarter\Database\Eloquent\Relations\HasOne<TRelatedModel, $this>
     */
    public function hasOne($related, $foreignKey = null, $localKey = null);

    /**
     * Define a has-one-through relationship.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     * @template TIntermediateModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  class-string<TIntermediateModel>  $through
     * @param  string|null  $firstKey
     * @param  string|null  $secondKey
     * @param  string|null  $localKey
     * @param  string|null  $secondLocalKey
     * @return \WpStarter\Database\Eloquent\Relations\HasOneThrough<TRelatedModel, TIntermediateModel, $this>
     */
    public function hasOneThrough($related, $through, $firstKey = null, $secondKey = null, $localKey = null, $secondLocalKey = null);

    /**
     * Define a polymorphic one-to-one relationship.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string  $name
     * @param  string|null  $type
     * @param  string|null  $id
     * @param  string|null  $localKey
     * @return \WpStarter\Database\Eloquent\Relations\MorphOne<TRelatedModel, $this>
     */
    public function morphOne($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * Define an inverse one-to-one or many relationship.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $ownerKey
     * @param  string|null  $relation
     * @return \WpStarter\Database\Eloquent\Relations\BelongsTo<TRelatedModel, $this>
     */
    public function belongsTo($related, $foreignKey = null, $ownerKey = null, $relation = null);

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string|null  $name
     * @param  string|null  $type
     * @param  string|null  $id
     * @param  string|null  $ownerKey
     * @return \WpStarter\Database\Eloquent\Relations\MorphTo<\WpStarter\Database\Eloquent\Contracts\Model, $this>
     */
    public function morphTo($name = null, $type = null, $id = null, $ownerKey = null);

    /**
     * Retrieve the actual class name for a given morph class.
     *
     * @param  string  $class
     * @return string
     */
    public static function getActualClassNameForMorph($class);

    /**
     * Create a pending has-many-through or has-one-through relationship.
     *
     * @template TIntermediateModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  string|\WpStarter\Database\Eloquent\Relations\HasMany<TIntermediateModel, covariant $this>|\WpStarter\Database\Eloquent\Relations\HasOne<TIntermediateModel, covariant $this>  $relationship
     * @return (
     *     $relationship is string
     *     ? \WpStarter\Database\Eloquent\PendingHasThroughRelationship<\WpStarter\Database\Eloquent\Contracts\Model, $this>
     *     : (
     *          $relationship is \WpStarter\Database\Eloquent\Relations\HasMany<TIntermediateModel, $this>
     *          ? \WpStarter\Database\Eloquent\PendingHasThroughRelationship<TIntermediateModel, $this, \WpStarter\Database\Eloquent\Relations\HasMany<TIntermediateModel, $this>>
     *          : \WpStarter\Database\Eloquent\PendingHasThroughRelationship<TIntermediateModel, $this, \WpStarter\Database\Eloquent\Relations\HasOne<TIntermediateModel, $this>>
     *     )
     * )
     */
    public function through($relationship);

    /**
     * Define a one-to-many relationship.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string|null  $foreignKey
     * @param  string|null  $localKey
     * @return \WpStarter\Database\Eloquent\Relations\HasMany<TRelatedModel, $this>
     */
    public function hasMany($related, $foreignKey = null, $localKey = null);

    /**
     * Define a has-many-through relationship.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     * @template TIntermediateModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  class-string<TIntermediateModel>  $through
     * @param  string|null  $firstKey
     * @param  string|null  $secondKey
     * @param  string|null  $localKey
     * @param  string|null  $secondLocalKey
     * @return \WpStarter\Database\Eloquent\Relations\HasManyThrough<TRelatedModel, TIntermediateModel, $this>
     */
    public function hasManyThrough($related, $through, $firstKey = null, $secondKey = null, $localKey = null, $secondLocalKey = null);

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string  $name
     * @param  string|null  $type
     * @param  string|null  $id
     * @param  string|null  $localKey
     * @return \WpStarter\Database\Eloquent\Relations\MorphMany<TRelatedModel, $this>
     */
    public function morphMany($related, $name, $type = null, $id = null, $localKey = null);

    /**
     * Define a many-to-many relationship.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string|class-string<\WpStarter\Database\Eloquent\Contracts\Model>|null  $table
     * @param  string|null  $foreignPivotKey
     * @param  string|null  $relatedPivotKey
     * @param  string|null  $parentKey
     * @param  string|null  $relatedKey
     * @param  string|null  $relation
     * @return \WpStarter\Database\Eloquent\Relations\BelongsToMany<TRelatedModel, $this, \WpStarter\Database\Eloquent\Relations\Pivot>
     */
    public function belongsToMany($related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $relation = null);

    /**
     * Define a polymorphic many-to-many relationship.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string  $name
     * @param  string|null  $table
     * @param  string|null  $foreignPivotKey
     * @param  string|null  $relatedPivotKey
     * @param  string|null  $parentKey
     * @param  string|null  $relatedKey
     * @param  string|null  $relation
     * @param  bool  $inverse
     * @return \WpStarter\Database\Eloquent\Relations\MorphToMany<TRelatedModel, $this>
     */
    public function morphToMany($related, $name, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $relation = null, $inverse = false);

    /**
     * Define a polymorphic, inverse many-to-many relationship.
     *
     * @template TRelatedModel of \WpStarter\Database\Eloquent\Contracts\Model
     *
     * @param  class-string<TRelatedModel>  $related
     * @param  string  $name
     * @param  string|null  $table
     * @param  string|null  $foreignPivotKey
     * @param  string|null  $relatedPivotKey
     * @param  string|null  $parentKey
     * @param  string|null  $relatedKey
     * @param  string|null  $relation
     * @return \WpStarter\Database\Eloquent\Relations\MorphToMany<TRelatedModel, $this>
     */
    public function morphedByMany($related, $name, $table = null, $foreignPivotKey = null, $relatedPivotKey = null, $parentKey = null, $relatedKey = null, $relation = null);

    /**
     * Get the joining table name for a many-to-many relation.
     *
     * @param  string  $related
     * @param  \WpStarter\Database\Eloquent\Contracts\Model|null  $instance
     * @return string
     */
    public function joiningTable($related, $instance = null);

    /**
     * Get this model's half of the intermediate table name for belongsToMany relationships.
     *
     * @return string
     */
    public function joiningTableSegment();

    /**
     * Determine if the model touches a given relation.
     *
     * @param  string  $relation
     * @return bool
     */
    public function touches($relation);

    /**
     * Touch the owning relations of the model.
     *
     * @return void
     */
    public function touchOwners();

    /**
     * Get the class name for polymorphic relations.
     *
     * @return string
     */
    public function getMorphClass();

    /**
     * Get all the loaded relations for the instance.
     *
     * @return array
     */
    public function getRelations();

    /**
     * Get a specified relationship.
     *
     * @param  string  $relation
     * @return mixed
     */
    public function getRelation($relation);

    /**
     * Determine if the given relation is loaded.
     *
     * @param  string  $key
     * @return bool
     */
    public function relationLoaded($key);

    /**
     * Set the given relationship on the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value);

    /**
     * Unset a loaded relationship.
     *
     * @param  string  $relation
     * @return $this
     */
    public function unsetRelation($relation);

    /**
     * Set the entire relations array on the model.
     *
     * @param  array  $relations
     * @return $this
     */
    public function setRelations(array $relations);

    /**
     * Enable relationship autoloading for this model.
     *
     * @return $this
     */
    public function withRelationshipAutoloading();

    /**
     * Duplicate the instance and unset all the loaded relations.
     *
     * @return $this
     */
    public function withoutRelations();

    /**
     * Duplicate the instance and unset the given loaded relations.
     *
     * @param  array|string  $relations
     * @return $this
     */
    public function withoutRelation($relations);

    /**
     * Unset all the loaded relations for the instance.
     *
     * @return $this
     */
    public function unsetRelations();

    /**
     * Get the relationships that are touched on save.
     *
     * @return array
     */
    public function getTouchedRelations();

    /**
     * Set the relationships that are touched on save.
     *
     * @param  array  $touches
     * @return $this
     */
    public function setTouchedRelations(array $touches);

    /**
     * Update the model's update timestamp.
     *
     * @param  array|string|null  $attribute
     * @return bool
     */
    public function touch($attribute = null);

    /**
     * Update the model's update timestamp without raising any events.
     *
     * @param  array|string|null  $attribute
     * @return bool
     */
    public function touchQuietly($attribute = null);

    /**
     * Update the creation and update timestamps.
     *
     * @return $this
     */
    public function updateTimestamps();

    /**
     * Set the value of the "created at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * Set the value of the "updated at" attribute.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedAt($value);

    /**
     * Get a fresh timestamp for the model.
     *
     * @return \WpStarter\Support\Carbon
     */
    public function freshTimestamp();

    /**
     * Get a fresh timestamp for the model.
     *
     * @return string
     */
    public function freshTimestampString();

    /**
     * Determine if the model uses timestamps.
     *
     * @return bool
     */
    public function usesTimestamps();

    /**
     * Get the name of the "created at" column.
     *
     * @return string|null
     */
    public function getCreatedAtColumn();

    /**
     * Get the name of the "updated at" column.
     *
     * @return string|null
     */
    public function getUpdatedAtColumn();

    /**
     * Get the fully-qualified "created at" column.
     *
     * @return string|null
     */
    public function getQualifiedCreatedAtColumn();

    /**
     * Get the fully-qualified "updated at" column.
     *
     * @return string|null
     */
    public function getQualifiedUpdatedAtColumn();

    /**
     * Disable timestamps for the current class during the given callback scope.
     *
     * @param  callable  $callback
     * @return mixed
     */
    public static function withoutTimestamps(callable $callback);

    /**
     * Disable timestamps for the given model classes during the given callback scope.
     *
     * @param  array  $models
     * @param  callable  $callback
     * @return mixed
     */
    public static function withoutTimestampsOn($models, $callback);

    /**
     * Determine if the given model is ignoring timestamps / touches.
     *
     * @param  string|null  $class
     * @return bool
     */
    public static function isIgnoringTimestamps($class = null);

    /**
     * Determine if the model uses unique ids.
     *
     * @return bool
     */
    public function usesUniqueIds();

    /**
     * Generate unique keys for the model.
     *
     * @return void
     */
    public function setUniqueIds();

    /**
     * Generate a new key for the model.
     *
     * @return string
     */
    public function newUniqueId();

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds();

    /**
     * Get the hidden attributes for the model.
     *
     * @return array<string>
     */
    public function getHidden();

    /**
     * Set the hidden attributes for the model.
     *
     * @param  array<string>  $hidden
     * @return $this
     */
    public function setHidden(array $hidden);

    /**
     * Merge new hidden attributes with existing hidden attributes on the model.
     *
     * @param  array<string>  $hidden
     * @return $this
     */
    public function mergeHidden(array $hidden);

    /**
     * Get the visible attributes for the model.
     *
     * @return array<string>
     */
    public function getVisible();

    /**
     * Set the visible attributes for the model.
     *
     * @param  array<string>  $visible
     * @return $this
     */
    public function setVisible(array $visible);

    /**
     * Merge new visible attributes with existing visible attributes on the model.
     *
     * @param  array<string>  $visible
     * @return $this
     */
    public function mergeVisible(array $visible);

    /**
     * Make the given, typically hidden, attributes visible.
     *
     * @param  array<string>|string|null  $attributes
     * @return $this
     */
    public function makeVisible($attributes);

    /**
     * Make the given, typically hidden, attributes visible if the given truth test passes.
     *
     * @param  bool|\Closure  $condition
     * @param  array<string>|string|null  $attributes
     * @return $this
     */
    public function makeVisibleIf($condition, $attributes);

    /**
     * Make the given, typically visible, attributes hidden.
     *
     * @param  array<string>|string|null  $attributes
     * @return $this
     */
    public function makeHidden($attributes);

    /**
     * Make the given, typically visible, attributes hidden if the given truth test passes.
     *
     * @param  bool|\Closure  $condition
     * @param  array<string>|string|null  $attributes
     * @return $this
     */
    public function makeHiddenIf($condition, $attributes);

    /**
     * Get the fillable attributes for the model.
     *
     * @return array<string>
     */
    public function getFillable();

    /**
     * Set the fillable attributes for the model.
     *
     * @param  array<string>  $fillable
     * @return $this
     */
    public function fillable(array $fillable);

    /**
     * Merge new fillable attributes with existing fillable attributes on the model.
     *
     * @param  array<string>  $fillable
     * @return $this
     */
    public function mergeFillable(array $fillable);

    /**
     * Get the guarded attributes for the model.
     *
     * @return array<string>
     */
    public function getGuarded();

    /**
     * Set the guarded attributes for the model.
     *
     * @param  array<string>  $guarded
     * @return $this
     */
    public function guard(array $guarded);

    /**
     * Merge new guarded attributes with existing guarded attributes on the model.
     *
     * @param  array<string>  $guarded
     * @return $this
     */
    public function mergeGuarded(array $guarded);

    /**
     * Disable all mass assignable restrictions.
     *
     * @param  bool  $state
     * @return void
     */
    public static function unguard($state = true);

    /**
     * Enable the mass assignment restrictions.
     *
     * @return void
     */
    public static function reguard();

    /**
     * Determine if the current state is "unguarded".
     *
     * @return bool
     */
    public static function isUnguarded();

    /**
     * Run the given callable while being unguarded.
     *
     * @template TReturn
     *
     * @param  callable(): TReturn  $callback
     * @return TReturn
     */
    public static function unguarded(callable $callback);

    /**
     * Determine if the given attribute may be mass assigned.
     *
     * @param  string  $key
     * @return bool
     */
    public function isFillable($key);

    /**
     * Determine if the given key is guarded.
     *
     * @param  string  $key
     * @return bool
     */
    public function isGuarded($key);

    /**
     * Determine if the model is totally guarded.
     *
     * @return bool
     */
    public function totallyGuarded();

    /**
     * Create a new resource object for the given resource.
     *
     * @param  class-string<\WpStarter\Http\Resources\Json\JsonResource>|null  $resourceClass
     * @return \WpStarter\Http\Resources\Json\JsonResource
     */
    public function toResource(?string $resourceClass = null): \WpStarter\Http\Resources\Json\JsonResource;

    /**
     * Guess the resource class name for the model.
     *
     * @return array{class-string<\WpStarter\Http\Resources\Json\JsonResource>, class-string<\WpStarter\Http\Resources\Json\JsonResource>}
     */
    public static function guessResourceName(): array;

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array<array-key, \WpStarter\Database\Eloquent\Contracts\Model>  $models
     * @return \WpStarter\Database\Eloquent\Collection<array-key, static>
     */
    public function newCollection(array $models = []);

    /**
     * Resolve the collection class name from the \WpStarter\Database\Eloquent\Attributes\CollectedBy attribute.
     *
     * @return class-string<\WpStarter\Database\Eloquent\Collection<array-key, static>>|null
     */
    public function resolveCollectionFromAttribute();
}
