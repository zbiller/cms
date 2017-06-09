<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    | Supported: "apc", "array", "database", "file", "memcached", "redis"
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same cache driver to group types of items stored in your caches.
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
        ],

        'array' => [
            'driver' => 'array',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached::OPT_CONNECT_TIMEOUT  => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing a RAM based store such as APC or Memcached, there might
    | be other applications utilizing the same cache. So, we'll specify a
    | value to get prefixed to all our keys so we can avoid collisions.
    |
    */

    'prefix' => 'laravel',

    /*
    |---------------------------------------------------------------------------------------------------
    | Query Caching Flag
    |---------------------------------------------------------------------------------------------------
    |
    | Every model by default implements a trait called IsCacheable.
    | That trait has the job of caching the database queries with Redis.
    | This way, on subsequent requests, the query results will be fetched from Redis, not the database.
    |
    */

    'query' => [

        /**
         * Flag indicating whether or not query caching should run.
         * By default, it's set to "false". If you want to enable it, set the "ENABLE_QUERY_CACHE=true" in .env
         *
         * IMPORTANT:
         * Please note that event if query caching is enabled inside the .env file some other constraints still apply:
         * 1. APP_ENV must not be "development" (in development no caching will happen, so debug is possible)
         */
        'enabled' => env('ENABLE_QUERY_CACHE', false),

        /**
         * The cache store used for query caching.
         * Please note that because cache tagging is used, "file" or "database" cache drivers are not available here.
         */
        'store' => 'redis',

        /**
         * The value to prefix all query cache tags.
         * This is not the general cache prefix (that is still the value of the key 'prefix' from this file).
         */
        'prefix' => 'cache.query',

    ],

];
