<?php

namespace App\Traits;

use App\Database\Builder;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait IsCacheable
{
    /**
     * Boot the model.
     *
     * @return void
     */
    public static function bootIsCacheable()
    {
        static::saved(function ($model) {
            $model->clearQueryCache();
        });

        static::deleted(function ($model) {
            $model->clearQueryCache();
        });
    }

    /**
     * @return string
     */
    public function getQueryCacheTag()
    {
        return CacheService::getQueryCachePrefix() . '.' . (string)$this->getTable();
    }

    /**
     * @return string
     */
    public function getDuplicateQueryCacheTag()
    {
        return CacheService::getDuplicateQueryCachePrefix() . '.' . (string)$this->getTable();
    }

    /**
     * Flush the query cache from Redis only for the tag corresponding to the model instance.
     *
     * @return void
     */
    public function clearQueryCache()
    {
        CacheService::clearQueryCache($this);
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return QueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        $cacheQueriesForever = false;
        $cacheOnlyDuplicateQueriesOnce = false;

        $connection = $this->getConnection();
        $grammar = $connection->getQueryGrammar();

        if (CacheService::canCacheQueries()) {
            if (CacheService::shouldCacheQueries()) {
                $cacheQueriesForever = true;
            }

            if (CacheService::shouldCacheDuplicateQueries()) {
                $cacheOnlyDuplicateQueriesOnce = true;
            }
        }

        if ($cacheQueriesForever === true) {
            return new Builder(
                $connection, $grammar, $connection->getPostProcessor(), $this->getQueryCacheTag(), CacheService::TYPE_CACHE_QUERIES
            );
        } elseif ($cacheOnlyDuplicateQueriesOnce === true) {
            return new Builder(
                $connection, $grammar, $connection->getPostProcessor(), $this->getDuplicateQueryCacheTag(), CacheService::TYPE_CACHE_DUPLICATE_QUERIES
            );
        }

        return parent::newBaseQueryBuilder();
    }
}