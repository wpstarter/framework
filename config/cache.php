<?php

use WpStarter\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache store that will be used by the
    | framework. This connection is utilized if another isn't explicitly
    | specified when running a cache operation inside the application.
    |
    */

    'default' => ws_env('CACHE_STORE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    | Supported drivers: "array", "database", "file", "memcached",
    |                    "redis", "dynamodb", "octane",
    |                    "failover", "null"
    |
    */

    'stores' => [

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'session' => [
            'driver' => 'session',
            'key' => ws_env('SESSION_CACHE_KEY', '_cache'),
        ],

        'database' => [
            'driver' => 'database',
            'connection' => ws_env('DB_CACHE_CONNECTION'),
            'table' => ws_env('DB_CACHE_TABLE', 'cache'),
            'lock_connection' => ws_env('DB_CACHE_LOCK_CONNECTION'),
            'lock_table' => ws_env('DB_CACHE_LOCK_TABLE'),
        ],

        'file' => [
            'driver' => 'file',
            'path' => ws_storage_path('framework/cache/data'),
            'lock_path' => ws_storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => ws_env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                ws_env('MEMCACHED_USERNAME'),
                ws_env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => ws_env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => ws_env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => ws_env('REDIS_CACHE_CONNECTION', 'cache'),
            'lock_connection' => ws_env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => ws_env('AWS_ACCESS_KEY_ID'),
            'secret' => ws_env('AWS_SECRET_ACCESS_KEY'),
            'region' => ws_env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => ws_env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => ws_env('DYNAMODB_ENDPOINT'),
        ],

        'octane' => [
            'driver' => 'octane',
        ],

        'failover' => [
            'driver' => 'failover',
            'stores' => [
                'database',
                'array',
            ],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, and DynamoDB cache
    | stores, there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => ws_env('CACHE_PREFIX', Str::slug((string) ws_env('APP_NAME', 'laravel'), '_').'_cache_'),

];
