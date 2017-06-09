<?php

namespace App\Database;

use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;

class Builder extends QueryBuilder
{
    /**
     * @var string
     */
    protected $cacheTag;

    /**
     * Create a new query builder instance.
     * Instantiate the cache tag value to be later used by Redis when storing the query in cache.
     *
     * @param ConnectionInterface $connection
     * @param Grammar|null $grammar
     * @param Processor|null $processor
     * @param string|null $cacheTag
     */
    public function __construct(ConnectionInterface $connection, Grammar $grammar = null, Processor $processor = null, $cacheTag = null)
    {
        parent::__construct($connection, $grammar, $processor);

        $this->cacheTag = $cacheTag;
    }

    /**
     * Returns a cache tag for Redis to use.
     * This tag is used so we know for which model/database_table the cache was set in Redis.
     *
     * @return string
     */
    protected function getCacheTag()
    {
        return $this->cacheTag;
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
        return cache()->store('redis')->tags($this->getCacheTag())->rememberForever($this->getCacheKey(), function() {
            return parent::runSelect();
        });
    }

    /**
     * Delete a record from the database.
     *
     * @param int|null $id
     * @return int
     */
    public function delete($id = null)
    {
        cache()->store('redis')->tags($this->getCacheTag())->flush();

        parent::delete($id);
    }

    /**
     * Run a truncate statement on the table.
     *
     * @return void
     */
    public function truncate()
    {
        cache()->store('redis')->tags($this->getCacheTag())->flush();

        parent::truncate();
    }
}