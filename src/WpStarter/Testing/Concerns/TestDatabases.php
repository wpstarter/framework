<?php

namespace WpStarter\Testing\Concerns;

use WpStarter\Database\QueryException;
use WpStarter\Foundation\Testing;
use WpStarter\Support\Arr;
use WpStarter\Support\Facades\Artisan;
use WpStarter\Support\Facades\DB;
use WpStarter\Support\Facades\ParallelTesting;
use WpStarter\Support\Facades\Schema;

trait TestDatabases
{
    /**
     * Indicates if the test database schema is up to date.
     *
     * @var bool
     */
    protected static $schemaIsUpToDate = false;

    /**
     * Boot a test database.
     *
     * @return void
     */
    protected function bootTestDatabase()
    {
        ParallelTesting::setUpProcess(function () {
            $this->whenNotUsingInMemoryDatabase(function ($database) {
                if (ParallelTesting::option('recreate_databases')) {
                    Schema::dropDatabaseIfExists(
                        $this->testDatabase($database)
                    );
                }
            });
        });

        ParallelTesting::setUpTestCase(function ($testCase) {
            $uses = array_flip(ws_class_uses_recursive(get_class($testCase)));

            $databaseTraits = [
                Testing\DatabaseMigrations::class,
                Testing\DatabaseTransactions::class,
                Testing\RefreshDatabase::class,
            ];

            if (Arr::hasAny($uses, $databaseTraits)) {
                if (! ParallelTesting::option('without_databases')) {
                    $this->whenNotUsingInMemoryDatabase(function ($database) use ($uses) {
                        [$testDatabase, $created] = $this->ensureTestDatabaseExists($database);

                        $this->switchToDatabase($testDatabase);

                        if (isset($uses[Testing\DatabaseTransactions::class])) {
                            $this->ensureSchemaIsUpToDate();
                        }

                        if ($created) {
                            ParallelTesting::callSetUpTestDatabaseCallbacks($testDatabase);
                        }
                    });
                }
            }
        });
    }

    /**
     * Ensure a test database exists and returns its name.
     *
     * @param  string  $database
     * @return array
     */
    protected function ensureTestDatabaseExists($database)
    {
        $testDatabase = $this->testDatabase($database);

        try {
            $this->usingDatabase($testDatabase, function () {
                Schema::hasTable('dummy');
            });
        } catch (QueryException $e) {
            $this->usingDatabase($database, function () use ($testDatabase) {
                Schema::dropDatabaseIfExists($testDatabase);
                Schema::createDatabase($testDatabase);
            });

            return [$testDatabase, true];
        }

        return [$testDatabase, false];
    }

    /**
     * Ensure the current database test schema is up to date.
     *
     * @return void
     */
    protected function ensureSchemaIsUpToDate()
    {
        if (! static::$schemaIsUpToDate) {
            Artisan::call('migrate');

            static::$schemaIsUpToDate = true;
        }
    }

    /**
     * Runs the given callable using the given database.
     *
     * @param  string  $database
     * @param  callable  $callable
     * @return void
     */
    protected function usingDatabase($database, $callable)
    {
        $original = DB::getConfig('database');

        try {
            $this->switchToDatabase($database);
            $callable();
        } finally {
            $this->switchToDatabase($original);
        }
    }

    /**
     * Apply the given callback when tests are not using in memory database.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function whenNotUsingInMemoryDatabase($callback)
    {
        $database = DB::getConfig('database');

        if ($database !== ':memory:') {
            $callback($database);
        }
    }

    /**
     * Switch to the given database.
     *
     * @param  string  $database
     * @return void
     */
    protected function switchToDatabase($database)
    {
        DB::purge();

        $default = ws_config('database.default');

        $url = ws_config("database.connections.{$default}.url");

        if ($url) {
            ws_config()->set(
                "database.connections.{$default}.url",
                preg_replace('/^(.*)(\/[\w-]*)(\??.*)$/', "$1/{$database}$3", $url),
            );
        } else {
            ws_config()->set(
                "database.connections.{$default}.database",
                $database,
            );
        }
    }

    /**
     * Returns the test database name.
     *
     * @return string
     */
    protected function testDatabase($database)
    {
        $token = ParallelTesting::token();

        return "{$database}_test_{$token}";
    }
}
