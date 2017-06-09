<?php

namespace App\Traits;

use App\Database\Builder;
use App\Services\CacheService;
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
        if (CacheService::canCacheQueries()) {
            $conn = $this->getConnection();
            $grammar = $conn->getQueryGrammar();

            return new Builder(
                $conn, $grammar, $conn->getPostProcessor(), $this->getQueryCacheTag()
            );
        }

        return parent::newBaseQueryBuilder();
    }
}