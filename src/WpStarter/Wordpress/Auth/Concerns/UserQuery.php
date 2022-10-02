<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 31-May-19
 * Time: 1:51 PM
 */

namespace WpStarter\Wordpress\Auth\Concerns;
use WpStarter\Support\Str;
use WpStarter\Support\Traits\ForwardsCalls;
use WpStarter\Database\ConnectionResolverInterface as Resolver;
use WpStarter\Database\Eloquent\Builder;
use WpStarter\Database\Eloquent\Collection;

trait UserQuery
{
    use ForwardsCalls;
    protected $connection;
    protected static $resolver;
    protected $perPage=15;

    /**
     * @param $model
     * @return bool
     */
    public function is($model)
    {
        return ! is_null($model) &&
            $this->getKey() === $model->getKey() &&
            $this->getTable() === $model->getTable() &&
            $this->getConnectionName() === $model->getConnectionName();
    }
    function getIncrementing(){
        return true;
    }
    function getKeyName(){
        return 'ID';
    }
    function getKey(){
        return $this->ID;
    }
    function getKeyType(){
        return 'int';
    }
    function getTable(){
        return 'users';
    }
    /**
     * Get the number of models to return per page.
     *
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * Set the number of models to return per page.
     *
     * @param  int  $perPage
     * @return $this
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;

        return $this;
    }


    /**
     * Qualify the given column name by the model's table.
     *
     * @param  string  $column
     * @return string
     */
    public function qualifyColumn($column)
    {
        if (Str::contains($column, '.')) {
            return $column;
        }
        return $this->getTable().'.'.$column;
    }
    /**
     * Get the table qualified key name.
     *
     * @return string
     */
    public function getQualifiedKeyName()
    {
        return $this->qualifyColumn($this->getKeyName());
    }

    /**
     * Determine if the model has a given scope.
     *
     * @param  string  $scope
     * @return bool
     */
    public function hasNamedScope($scope)
    {
        return method_exists($this, 'scope'.ucfirst($scope));
    }

    /**
     * Apply the given named scope if possible.
     *
     * @param  string  $scope
     * @param  array  $parameters
     * @return mixed
     */
    public function callNamedScope($scope, array $parameters = [])
    {
        return $this->{'scope'.ucfirst($scope)}(...$parameters);
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new static((array) $attributes);

        $model->setConnection(
            $this->getConnection()->getName()
        );


        return $model;
    }
    /**
     * Create a new Eloquent Collection instance.
     *
     * @param  array  $models
     * @return \WpStarter\Database\Eloquent\Collection
     */
    public function newCollection(array $models = [])
    {
        return new Collection($models);
    }
    /**
     * Create a new model instance that is existing.
     *
     * @param  array  $attributes
     * @param  string|null  $connection
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $model = $this->newInstance([], true);

        $model->init((object)$attributes);

        $model->fireModelEvent('retrieved', false);

        return $model;
    }
    /**
     * Begin querying the model.
     *
     * @return \WpStarter\Database\Eloquent\Builder
     */
    public static function query()
    {
        return (new static)->newQuery();
    }
    /**
     * Get a new query builder for the model's table.
     *
     * @return \WpStarter\Database\Eloquent\Builder
     */
    public function newQuery()
    {
        return $this->newQueryWithoutScopes();
    }
    /**
     * Get a new query builder that doesn't have any global scopes.
     *
     * @return \WpStarter\Database\Eloquent\Builder|static
     */
    public function newQueryWithoutScopes()
    {
        return $this->newModelQuery();
    }
    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \WpStarter\Database\Query\Builder  $query
     * @return QueryBuilder|static
     */
    public function newUserQueryBuilder($query)
    {
        return new QueryBuilder($query);
    }
    /**
     * Get a new query builder that doesn't have any global scopes or eager loading.
     *
     * @return \WpStarter\Database\Eloquent\Builder|static
     */
    public function newModelQuery()
    {
        return $this->newUserQueryBuilder(
            $this->newBaseQueryBuilder()
        )->setModel($this);
    }
    /**
     * Get a new query builder instance for the connection.
     *
     * @return \WpStarter\Database\Query\Builder
     */
    protected function newBaseQueryBuilder()
    {
        return $this->getConnection()->query();
    }
    /**
     * Get the database connection for the model.
     *
     * @return \WpStarter\Database\Connection
     */
    public function getConnection()
    {
        return static::resolveConnection($this->getConnectionName());
    }

    /**
     * Get the current connection name for the model.
     *
     * @return string
     */
    public function getConnectionName()
    {
        return $this->connection;
    }

    /**
     * Set the connection associated with the model.
     *
     * @param  string  $name
     * @return $this
     */
    public function setConnection($name)
    {
        $this->connection = $name;

        return $this;
    }

    /**
     * Resolve a connection instance.
     *
     * @param  string|null  $connection
     * @return \WpStarter\Database\Connection
     */
    public static function resolveConnection($connection = null)
    {
        return static::$resolver->connection($connection);
    }

    /**
     * Get the connection resolver instance.
     *
     * @return \WpStarter\Database\ConnectionResolverInterface
     */
    public static function getConnectionResolver()
    {
        return static::$resolver;
    }

    /**
     * Set the connection resolver instance.
     *
     * @param  \WpStarter\Database\ConnectionResolverInterface  $resolver
     * @return void
     */
    public static function setConnectionResolver(Resolver $resolver)
    {
        static::$resolver = $resolver;
    }

    /**
     * Unset the connection resolver for models.
     *
     * @return void
     */
    public static function unsetConnectionResolver()
    {
        static::$resolver = null;
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
        if (in_array($method, ['increment', 'decrement'])) {
            return $this->$method(...$parameters);
        }

        return $this->forwardCallTo($this->newQuery(), $method, $parameters);
    }

    /**
     * Handle dynamic static method calls into the method.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    /**
     * This find function will use wp core function to do and have object caching by default
     * @param $id
     * @param $site_id
     * @return static|null
     */
    public static function find($id,$site_id=''){
        if($id instanceof static){
            $id=$id->ID;
        }
        $data=static::get_data_by('id',$id);

        if($data){
            $model = new static();
            $model->init($data,$site_id);
            return $model;
        }
        return null;
    }

}