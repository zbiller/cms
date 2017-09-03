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
    | Query Caching
    |---------------------------------------------------------------------------------------------------
    |
    | Every model by default implements a trait called IsCacheable.
    | That trait has the job of caching the database queries with Redis forever.
    | This way, on subsequent requests, the query results will be fetched from Redis, not the database.
    | Besides caching queries with Redis, you can also choose to only cache duplicate queries for the current request.
    |
    */

    'query' => [

        /*
        |
        | Flag indicating whether or not query caching should using run (forever cache).
        | By default, it's set to "false". If you want to enable it, set the "CACHE_QUERIES=true" in .env file.
        |
        | IMPORTANT:
        | Please note that even if query caching is enabled inside the .env file some other constraints still apply:
        | 1. APP_ENV must not be "development" (in development mode no caching will happen, so debug is possible).
        |
        */
        'cache_queries' => env('ENABLE_QUERY_CACHE', false),

        /*
        |
        | Flag indicating whether or not query caching only on duplicate queries should run (only for the current request).
        | By default, it's set to "false". If you want to enable it, set the "CACHE_DUPLICATE_QUERIES=true" in .env file.
        |
        | IMPORTANT:
        | Please note that even if query caching is enabled inside the .env file some other constraints still apply:
        | 1. APP_ENV must not be "development" (in development mode no caching will happen, so debug is possible).
        |
        */
        'cache_duplicate_queries' => env('ENABLE_DUPLICATE_QUERY_CACHE', false),

        /*
        |
        | The cache store used for query caching ("cache_queries" option).
        | Please note that because cache tagging is used, "file" or "database" cache drivers are not available here.
        |
        */
        'query_store' => 'redis',

        /*
        |
        | The cache store used for query caching ("cache_duplicate_queries" option).
        | Please note that because cache tagging is used, "file" or "database" cache drivers are not available here.
        |
        */
        'duplicate_query_store' => 'array',

        /*
        |
        | The value to prefix all query cache tags ("cache_queries" option).
        | This is not the general cache prefix (that is still the value of the key 'prefix' from this file).
        | This value only acts as prefix for query cache tags.
        |
        */
        'query_prefix' => 'cache.query',

        /*
        |
        | The value to prefix all query cache tags ("cache_duplicate_queries" option).
        | This is not the general cache prefix (that is still the value of the key 'prefix' from this file).
        | This value only acts as prefix for query cache tags.
        |
        */
        'duplicate_query_prefix' => 'cache.duplicate_query',

    ],

];
