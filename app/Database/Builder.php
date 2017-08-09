<?php

namespace App\Database;

use App\Services\CacheService;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

class Builder extends QueryBuilder
{
    /**
     * The cache tag value.
     * The value comes from the App/Traits/IsCacheable.php file.
     *
     * @var string
     */
    protected $cacheTag;

    /**
     * The cache type value.
     * Can have one of the values presented below by the TYPE_CACHE constants.
     * The value comes from the App/Traits/IsCacheable.php file.
     *
     * @var string
     */
    protected $cacheType;

    /**
     * Create a new query builder instance.
     *
     * @param ConnectionInterface $connection
     * @param Grammar|null $grammar
     * @param Processor|null $processor
     * @param string|null $cacheTag
     * @param int $cacheType
     */
    public function __construct(ConnectionInterface $connection, Grammar $grammar = null, Processor $processor = null, $cacheTag = null, $cacheType = null)
    {
        parent::__construct($connection, $grammar, $processor);

        $this->cacheType = $cacheType;
        $this->cacheTag = $cacheTag;
    }

    /**
     * Returns a unique string that can identify this query.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return json_encode([
            $this->toSql() => $this->getBindings()
        ]);
    }

    /**
     * Run the query as a "select" statement against the connection.
     *
     * @return array
     */
    protected function runSelect()
    {
        switch ($this->cacheType) {
            case CacheService::TYPE_CACHE_QUERIES:
                return cache()->store(CacheService::getQueryCacheStore())->tags($this->cacheTag)->rememberForever($this->getCacheKey(), function() {
                    return parent::runSelect();
                });

                break;
            case CacheService::TYPE_CACHE_DUPLICATE_QUERIES:
                return cache()->store(CacheService::getDuplicateQueryCacheStore())->tags($this->cacheTag)->remember($this->getCacheKey(), 1, function() {
                    return parent::runSelect();
                });

                break;
            default:
                return parent::runSelect();
                break;
        }
    }

    /**
     * Insert a new record into the database.
     *
     * @param array $values
     * @return bool
     */
    public function insert(array $values)
    {
        cache()->store(CacheService::getQueryCacheStore())->tags($this->cacheTag)->flush();

        return parent::insert($values);
    }

    /**
     * Update a record in the database.
     *
     * @param array $values
     * @return int
     */
    public function update(array $values)
    {
        cache()->store(CacheService::getQueryCacheStore())->tags($this->cacheTag)->flush();

        return parent::update($values);
    }

    /**
     * Delete a record from the database.
     *
     * @param int|null $id
     * @return int
     */
    public function delete($id = null)
    {
        cache()->store(CacheService::getQueryCacheStore())->tags($this->cacheTag)->flush();

        parent::delete($id);
    }

    /**
     * Run a truncate statement on the table.
     *
     * @return void
     */
    public function truncate()
    {
        cache()->store(CacheService::getQueryCacheStore())->tags($this->cacheTag)->flush();

        parent::truncate();
    }
}