<?php

namespace App\Traits;

use App\Database\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait IsCacheable
{
    /**
     * Get a new query builder instance for the connection.
     *
     * @return QueryBuilder
     */
    protected function newBaseQueryBuilder()
    {
        $conn = $this->getConnection();
        $grammar = $conn->getQueryGrammar();

        return new Builder($conn, $grammar, $conn->getPostProcessor());
    }
}