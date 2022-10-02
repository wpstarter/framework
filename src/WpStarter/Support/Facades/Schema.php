<?php

namespace WpStarter\Support\Facades;

/**
 * @method static \WpStarter\Database\Schema\Builder create(string $table, \Closure $callback)
 * @method static \WpStarter\Database\Schema\Builder createDatabase(string $name)
 * @method static \WpStarter\Database\Schema\Builder disableForeignKeyConstraints()
 * @method static \WpStarter\Database\Schema\Builder drop(string $table)
 * @method static \WpStarter\Database\Schema\Builder dropDatabaseIfExists(string $name)
 * @method static \WpStarter\Database\Schema\Builder dropIfExists(string $table)
 * @method static \WpStarter\Database\Schema\Builder enableForeignKeyConstraints()
 * @method static \WpStarter\Database\Schema\Builder rename(string $from, string $to)
 * @method static \WpStarter\Database\Schema\Builder table(string $table, \Closure $callback)
 * @method static bool hasColumn(string $table, string $column)
 * @method static bool hasColumns(string $table, array $columns)
 * @method static bool dropColumns(string $table, array $columns)
 * @method static bool hasTable(string $table)
 * @method static void defaultStringLength(int $length)
 * @method static void registerCustomDoctrineType(string $class, string $name, string $type)
 * @method static array getColumnListing(string $table)
 * @method static string getColumnType(string $table, string $column)
 * @method static void morphUsingUuids()
 * @method static \WpStarter\Database\Connection getConnection()
 * @method static \WpStarter\Database\Schema\Builder setConnection(\WpStarter\Database\Connection $connection)
 *
 * @see \WpStarter\Database\Schema\Builder
 */
class Schema extends Facade
{
    /**
     * Get a schema builder instance for a connection.
     *
     * @param  string|null  $name
     * @return \WpStarter\Database\Schema\Builder
     */
    public static function connection($name)
    {
        return static::$app['db']->connection($name)->getSchemaBuilder();
    }

    /**
     * Get a schema builder instance for the default connection.
     *
     * @return \WpStarter\Database\Schema\Builder
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['db']->connection()->getSchemaBuilder();
    }
}
