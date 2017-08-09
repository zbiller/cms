<?php

namespace App\Services;

use App\Sniffers\ModelSniffer;
use App\Traits\IsCacheable;
use Exception;
use Illuminate\Database\Eloquent\Model;

class CacheService
{
    /**
     * The model the cache should run on.
     * The model should use the IsCacheable trait for the whole process to work.
     *
     * @var Model
     */
    protected static $model;

    /**
     * Flag whether or not to cache queries forever.
     *
     * @var bool
     */
    protected static $cacheQueries = true;

    /**
     * Flag whether or not to cache only duplicate queries for the current request.
     *
     * @var bool
     */
    protected static $cacheDuplicateQueries = true;

    /**
     * The query cache types available inside App/Services/CacheService.php
     */
    const TYPE_CACHE_QUERIES = 1;
    const TYPE_CACHE_DUPLICATE_QUERIES = 2;

    /**
     * Get the cache store to be used when caching queries forever.
     *
     * @return string
     */
    public static function getQueryCacheStore()
    {
        return config('cache.query.query_store') ?: 'redis';
    }

    /**
     * Get the cache store to be used when caching only duplicate queries.
     *
     * @return string
     */
    public static function getDuplicateQueryCacheStore()
    {
        return config('cache.query.duplicate_query_store') ?: 'array';
    }

    /**
     * Get the cache prefix to be appended to the specific cache tag for the model instance.
     * Used when caching queries forever.
     *
     * @return string
     */
    public static function getQueryCachePrefix()
    {
        return config('cache.query.query_prefix');
    }

    /**
     * Get the cache prefix to be appended to the specific cache tag for the model instance.
     * Used when caching only duplicate queries.
     *
     * @return string
     */
    public static function getDuplicateQueryCachePrefix()
    {
        return config('cache.query.duplicate_query_prefix');
    }

    /**
     * Verify if any type of caching should run.
     * The only requirement here is that the environment is not development.
     * In development it's better suited if caching is disabled, for debug purposes.
     *
     * @return bool
     */
    public static function shouldCache()
    {
        return app()->environment() != 'development';
    }

    /**
     * Verify if forever query caching should run.
     *
     * @return bool
     */
    public static function shouldCacheQueries()
    {
        return self::shouldCache() && config('cache.query.cache_queries') === true;
    }

    /**
     * Verify if caching of duplicate queries should run.
     *
     * @return bool
     */
    public static function shouldCacheDuplicateQueries()
    {
        return self::shouldCache() && config('cache.query.cache_duplicate_queries') === true;
    }

    /**
     * Flush all the query cache for the specified store (redis).
     * Please note that this does not happen only for one caching type, but for all.
     *
     * @throws Exception
     */
    public static function flushAllQueryCache()
    {
        if (!self::canCacheQueries()) {
            return;
        }

        if (self::shouldCacheQueries()) {
            cache()->store(self::getQueryCacheStore())->flush();
        }
    }

    /**
     * Flush the query cache from Redis only for the tag corresponding to the model instance.
     * If something fails, flush all existing cache for the specified store (redis).
     * This way, it's guaranteed that nothing will be out of sync at the database level.
     *
     * @param Model $model
     * @return void
     */
    public static function clearQueryCache(Model $model)
    {
        if (!((self::shouldCacheQueries() || self::shouldCacheDuplicateQueries()) && self::canCacheQueries())) {
            return;
        }

        try {
            self::$model = $model;

            cache()->store(self::getQueryCacheStore())->tags(self::$model->getQueryCacheTag())->flush();

            foreach ((new ModelSniffer())->getAllRelations(self::$model) as $relation => $attributes) {
                if (
                    isset($attributes['model']) && ($related = $attributes['model']) &&
                    $related instanceof Model && array_key_exists(IsCacheable::class, class_uses($related))
                ) {
                    cache()->store(self::getQueryCacheStore())->tags(self::$model->getQueryCacheTag())->flush();
                }
            }
        } catch (Exception $e) {
            self::flushAllQueryCache();
        }
    }

    /**
     * Verify if either forever query caching or duplicate query caching are enabled.
     *
     * @return bool
     */
    public static function canCacheQueries()
    {
        return self::$cacheQueries === true || self::$cacheDuplicateQueries === true;
    }

    /**
     * Disable caching of database queries for the current request.
     * This is generally useful when working with rolled back database migrations.
     *
     * @return void
     */
    public static function disableQueryCache()
    {
        self::$cacheQueries = self::$cacheDuplicateQueries = false;
    }
}