<?php

use WpStarter\Support\Str;
use Pdo\Mysql;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for database operations. This is
    | the connection which will be utilized unless another connection
    | is explicitly specified when you execute a query / statement.
    |
    */

    'default' => ws_env('DB_CONNECTION', 'sqlite'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Below are all of the database connections defined for your application.
    | An example configuration is provided for each database system which
    | is supported by Laravel. You're free to add / remove connections.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => ws_env('DB_URL'),
            'database' => ws_env('DB_DATABASE', ws_database_path('database.sqlite')),
            'prefix' => '',
            'prefix_indexes' => null,
            'foreign_key_constraints' => ws_env('DB_FOREIGN_KEYS', true),
            'busy_timeout' => null,
            'journal_mode' => null,
            'synchronous' => null,
            'transaction_mode' => 'DEFERRED',
            'pragmas' => [],
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => ws_env('DB_URL'),
            'host' => ws_env('DB_HOST', '127.0.0.1'),
            'port' => ws_env('DB_PORT', '3306'),
            'database' => ws_env('DB_DATABASE', 'laravel'),
            'username' => ws_env('DB_USERNAME', 'root'),
            'password' => ws_env('DB_PASSWORD', ''),
            'unix_socket' => ws_env('DB_SOCKET', ''),
            'charset' => ws_env('DB_CHARSET', 'utf8mb4'),
            'collation' => ws_env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                Mysql::ATTR_SSL_CA => ws_env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mariadb' => [
            'driver' => 'mariadb',
            'url' => ws_env('DB_URL'),
            'host' => ws_env('DB_HOST', '127.0.0.1'),
            'port' => ws_env('DB_PORT', '3306'),
            'database' => ws_env('DB_DATABASE', 'laravel'),
            'username' => ws_env('DB_USERNAME', 'root'),
            'password' => ws_env('DB_PASSWORD', ''),
            'unix_socket' => ws_env('DB_SOCKET', ''),
            'charset' => ws_env('DB_CHARSET', 'utf8mb4'),
            'collation' => ws_env('DB_COLLATION', 'utf8mb4_unicode_ci'),
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                Mysql::ATTR_SSL_CA => ws_env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'pgsql' => [
            'driver' => 'pgsql',
            'url' => ws_env('DB_URL'),
            'host' => ws_env('DB_HOST', '127.0.0.1'),
            'port' => ws_env('DB_PORT', '5432'),
            'database' => ws_env('DB_DATABASE', 'laravel'),
            'username' => ws_env('DB_USERNAME', 'root'),
            'password' => ws_env('DB_PASSWORD', ''),
            'charset' => ws_env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            'search_path' => 'public',
            'sslmode' => 'prefer',
        ],

        'sqlsrv' => [
            'driver' => 'sqlsrv',
            'url' => ws_env('DB_URL'),
            'host' => ws_env('DB_HOST', 'localhost'),
            'port' => ws_env('DB_PORT', '1433'),
            'database' => ws_env('DB_DATABASE', 'laravel'),
            'username' => ws_env('DB_USERNAME', 'root'),
            'password' => ws_env('DB_PASSWORD', ''),
            'charset' => ws_env('DB_CHARSET', 'utf8'),
            'prefix' => '',
            'prefix_indexes' => true,
            // 'encrypt' => env('DB_ENCRYPT', 'yes'),
            // 'trust_server_certificate' => env('DB_TRUST_SERVER_CERTIFICATE', 'false'),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run on the database.
    |
    */

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as Memcached. You may define your connection settings here.
    |
    */

    'redis' => [

        'client' => ws_env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => ws_env('REDIS_CLUSTER', 'redis'),
            'prefix' => ws_env('REDIS_PREFIX', Str::slug((string) ws_env('APP_NAME', 'laravel'), '_').'_database_'),
            'persistent' => ws_env('REDIS_PERSISTENT', false),
        ],

        'default' => [
            'url' => ws_env('REDIS_URL'),
            'host' => ws_env('REDIS_HOST', '127.0.0.1'),
            'username' => ws_env('REDIS_USERNAME'),
            'password' => ws_env('REDIS_PASSWORD'),
            'port' => ws_env('REDIS_PORT', '6379'),
            'database' => ws_env('REDIS_DB', '0'),
            'max_retries' => ws_env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => ws_env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => ws_env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => ws_env('REDIS_BACKOFF_CAP', 1000),
        ],

        'cache' => [
            'url' => ws_env('REDIS_URL'),
            'host' => ws_env('REDIS_HOST', '127.0.0.1'),
            'username' => ws_env('REDIS_USERNAME'),
            'password' => ws_env('REDIS_PASSWORD'),
            'port' => ws_env('REDIS_PORT', '6379'),
            'database' => ws_env('REDIS_CACHE_DB', '1'),
            'max_retries' => ws_env('REDIS_MAX_RETRIES', 3),
            'backoff_algorithm' => ws_env('REDIS_BACKOFF_ALGORITHM', 'decorrelated_jitter'),
            'backoff_base' => ws_env('REDIS_BACKOFF_BASE', 100),
            'backoff_cap' => ws_env('REDIS_BACKOFF_CAP', 1000),
        ],

    ],

];
