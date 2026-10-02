<?php

namespace WpStarter\Database\Schema;

use WpStarter\Support\Stringable;

class ForeignIdColumnDefinition extends ColumnDefinition
{
    /**
     * The schema builder blueprint instance.
     *
     * @var \WpStarter\Database\Schema\Blueprint
     */
    protected $blueprint;

    /**
     * Create a new foreign ID column definition.
     *
     * @param  \WpStarter\Database\Schema\Blueprint  $blueprint
     * @param  array  $attributes
     */
    public function __construct(Blueprint $blueprint, $attributes = [])
    {
        parent::__construct($attributes);

        $this->blueprint = $blueprint;
    }

    /**
     * Create a foreign key constraint on this column referencing the "id" column of the conventionally related table.
     *
     * @param  string|null  $table
     * @param  string|null  $column
     * @param  string|null  $indexName
     * @return \WpStarter\Database\Schema\ForeignKeyDefinition
     */
    public function constrained($table = null, $column = null, $indexName = null)
    {
        $table ??= $this->table;
        $column ??= $this->referencesModelColumn ?? 'id';

        return $this->references($column, $indexName)->on($table ?? (new Stringable($this->name))->beforeLast('_'.$column)->plural());
    }

    /**
     * Specify which column this foreign ID references on another table.
     *
     * @param  string  $column
     * @param  string|null  $indexName
     * @return \WpStarter\Database\Schema\ForeignKeyDefinition
     */
    public function references($column, $indexName = null)
    {
        return $this->blueprint->foreign($this->name, $indexName)->references($column);
    }
}
