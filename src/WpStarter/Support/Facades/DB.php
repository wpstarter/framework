<?php

namespace WpStarter\Support\Facades;

use WpStarter\Database\Console\Migrations\FreshCommand;
use WpStarter\Database\Console\Migrations\RefreshCommand;
use WpStarter\Database\Console\Migrations\ResetCommand;
use WpStarter\Database\Console\Migrations\RollbackCommand;
use WpStarter\Database\Console\WipeCommand;

/**
 * @method static \WpStarter\Database\Connection connection(\UnitEnum|string|null $name = null)
 * @method static \WpStarter\Database\ConnectionInterface build(array $config)
 * @method static string calculateDynamicConnectionName(array $config)
 * @method static \WpStarter\Database\ConnectionInterface connectUsing(\UnitEnum|string $name, array $config, bool $force = false)
 * @method static void purge(\UnitEnum|string|null $name = null)
 * @method static void disconnect(\UnitEnum|string|null $name = null)
 * @method static \WpStarter\Database\Connection reconnect(\UnitEnum|string|null $name = null)
 * @method static mixed usingConnection(\UnitEnum|string $name, callable $callback)
 * @method static string getDefaultConnection()
 * @method static void setDefaultConnection(string $name)
 * @method static string[] supportedDrivers()
 * @method static string[] availableDrivers()
 * @method static void extend(string $name, callable $resolver)
 * @method static void forgetExtension(string $name)
 * @method static array getConnections()
 * @method static void setReconnector(callable $reconnector)
 * @method static \WpStarter\Database\DatabaseManager setApplication(\WpStarter\Contracts\Foundation\Application $app)
 * @method static void macro(string $name, object|callable $macro)
 * @method static void mixin(object $mixin, bool $replace = true)
 * @method static bool hasMacro(string $name)
 * @method static void flushMacros()
 * @method static mixed macroCall(string $method, array $parameters)
 * @method static void useDefaultQueryGrammar()
 * @method static void useDefaultSchemaGrammar()
 * @method static void useDefaultPostProcessor()
 * @method static \WpStarter\Database\Schema\Builder getSchemaBuilder()
 * @method static \WpStarter\Database\Query\Builder table(\Closure|\WpStarter\Database\Query\Builder|\WpStarter\Contracts\Database\Query\Expression|\UnitEnum|string $table, string|null $as = null)
 * @method static \WpStarter\Database\Query\Builder query()
 * @method static mixed selectOne(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static mixed scalar(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static array selectFromWriteConnection(string $query, array $bindings = [])
 * @method static array select(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static array selectResultSets(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static \Generator cursor(string $query, array $bindings = [], bool $useReadPdo = true)
 * @method static bool insert(string $query, array $bindings = [])
 * @method static int update(string $query, array $bindings = [])
 * @method static int delete(string $query, array $bindings = [])
 * @method static bool statement(string $query, array $bindings = [])
 * @method static int affectingStatement(string $query, array $bindings = [])
 * @method static bool unprepared(string $query)
 * @method static int|null threadCount()
 * @method static array[] pretend(\Closure $callback)
 * @method static mixed withoutPretending(\Closure $callback)
 * @method static void bindValues(\PDOStatement $statement, array $bindings)
 * @method static array prepareBindings(array $bindings)
 * @method static void logQuery(string $query, array $bindings, float|null $time = null)
 * @method static void whenQueryingForLongerThan(\DateTimeInterface|\Carbon\CarbonInterval|float|int $threshold, callable $handler)
 * @method static void allowQueryDurationHandlersToRunAgain()
 * @method static float totalQueryDuration()
 * @method static void resetTotalQueryDuration()
 * @method static void reconnectIfMissingConnection()
 * @method static \WpStarter\Database\Connection beforeStartingTransaction(\Closure $callback)
 * @method static \WpStarter\Database\Connection beforeExecuting(\Closure $callback)
 * @method static void listen(\Closure $callback)
 * @method static \WpStarter\Contracts\Database\Query\Expression raw(mixed $value)
 * @method static string escape(string|float|int|bool|null $value, bool $binary = false)
 * @method static bool hasModifiedRecords()
 * @method static void recordsHaveBeenModified(bool $value = true)
 * @method static \WpStarter\Database\Connection setRecordModificationState(bool $value)
 * @method static void forgetRecordModificationState()
 * @method static \WpStarter\Database\Connection useWriteConnectionWhenReading(bool $value = true)
 * @method static \PDO getPdo()
 * @method static \PDO|\Closure|null getRawPdo()
 * @method static \PDO getReadPdo()
 * @method static \PDO|\Closure|null getRawReadPdo()
 * @method static \WpStarter\Database\Connection setPdo(\PDO|\Closure|null $pdo)
 * @method static \WpStarter\Database\Connection setReadPdo(\PDO|\Closure|null $pdo)
 * @method static \WpStarter\Database\Connection setReadPdoConfig(array $config)
 * @method static string|null getName()
 * @method static string|null getNameWithReadWriteType()
 * @method static mixed getConfig(string|null $option = null)
 * @method static string getDriverName()
 * @method static string getDriverTitle()
 * @method static \WpStarter\Database\Query\Grammars\Grammar getQueryGrammar()
 * @method static \WpStarter\Database\Connection setQueryGrammar(\WpStarter\Database\Query\Grammars\Grammar $grammar)
 * @method static \WpStarter\Database\Schema\Grammars\Grammar getSchemaGrammar()
 * @method static \WpStarter\Database\Connection setSchemaGrammar(\WpStarter\Database\Schema\Grammars\Grammar $grammar)
 * @method static \WpStarter\Database\Query\Processors\Processor getPostProcessor()
 * @method static \WpStarter\Database\Connection setPostProcessor(\WpStarter\Database\Query\Processors\Processor $processor)
 * @method static \WpStarter\Contracts\Events\Dispatcher|null getEventDispatcher()
 * @method static \WpStarter\Database\Connection setEventDispatcher(\WpStarter\Contracts\Events\Dispatcher $events)
 * @method static void unsetEventDispatcher()
 * @method static \WpStarter\Database\Connection setTransactionManager(\WpStarter\Database\DatabaseTransactionsManager $manager)
 * @method static void unsetTransactionManager()
 * @method static bool pretending()
 * @method static array[] getQueryLog()
 * @method static array getRawQueryLog()
 * @method static void flushQueryLog()
 * @method static void enableQueryLog()
 * @method static void disableQueryLog()
 * @method static bool logging()
 * @method static string getDatabaseName()
 * @method static \WpStarter\Database\Connection setDatabaseName(string $database)
 * @method static \WpStarter\Database\Connection setReadWriteType(string|null $readWriteType)
 * @method static string getTablePrefix()
 * @method static \WpStarter\Database\Connection setTablePrefix(string $prefix)
 * @method static mixed withoutTablePrefix(\Closure $callback)
 * @method static string getServerVersion()
 * @method static void resolverFor(string $driver, \Closure $callback)
 * @method static \Closure|null getResolver(string $driver)
 * @method static mixed transaction(\Closure $callback, int $attempts = 1)
 * @method static void beginTransaction()
 * @method static void commit()
 * @method static void rollBack(int|null $toLevel = null)
 * @method static int transactionLevel()
 * @method static void afterCommit(callable $callback)
 * @method static void afterRollBack(callable $callback)
 *
 * @see \WpStarter\Database\DatabaseManager
 */
class DB extends Facade
{
    /**
     * Indicate if destructive Artisan commands should be prohibited.
     *
     * Prohibits: db:wipe, migrate:fresh, migrate:refresh, migrate:reset, and migrate:rollback
     *
     * @param  bool  $prohibit
     * @return void
     */
    public static function prohibitDestructiveCommands(bool $prohibit = true)
    {
        FreshCommand::prohibit($prohibit);
        RefreshCommand::prohibit($prohibit);
        ResetCommand::prohibit($prohibit);
        RollbackCommand::prohibit($prohibit);
        WipeCommand::prohibit($prohibit);
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'db';
    }
}
