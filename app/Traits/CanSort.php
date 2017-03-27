<?php

namespace App\Traits;

use App\Http\Sorts\Sort;
use App\Exceptions\SortException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CanSort
{
    /**
     * The query builder instance from the Sorted scope.
     *
     * @var Builder
     */
    protected $sortQuery;

    /**
     * The App\Http\Sorts\Sort instance.
     * This is used to get the sorting rules, just like a request.
     *
     * @var Sort
     */
    protected $sortInstance;

    /**
     * The request object passed from the controller applying the "filtered" scope on a model.
     *
     * @var Request
     */
    protected $sortRequest;

    /**
     * The field to sort by.
     *
     * @var string
     */
    protected $sortField = Sort::DEFAULT_SORT_FIELD;

    /**
     * The direction to sort in.
     *
     * @var string
     */
    protected $sortDirection = Sort::DEFAULT_DIRECTION_FIELD;

    /**
     * The filter scope.
     * Should be called on the model when building the query.
     *
     * @param Builder $query
     * @param Request $request
     * @param Sort $sort
     * @throws SortException
     */
    public function scopeSorted($query, Request $request, Sort $sort = null)
    {
        $this->sortQuery = $query;
        $this->sortRequest = $request;
        $this->sortInstance = $sort;

        $this->setFieldToSortBy();
        $this->setDirectionToSortIn();

        if ($this->isValidSort()) {
            $this->checkSortingDirection();

            if ($this->sortRequest->get($this->sortDirection) == Sort::DIRECTION_RANDOM) {
                $this->sortQuery->inRandomOrder();
            } else {
                if ($this->shouldSortByRelation()) {
                    $this->sortByRelation();
                } else {
                    $this->sortNormally();
                }
            }
        }
    }

    /**
     * Sort model records using columns from the model relation's table.
     *
     * @return void
     */
    private function sortByRelation()
    {
        list($relation, $field) = explode('.', $this->sortRequest->get($this->sortField));

        $this->checkRelationToSortBy($relation);

        $modelTable = $this->getTable();
        $relationTable = $this->{$relation}()->getModel()->getTable();
        $foreignKey = $this->{$relation}() instanceof HasOne ?
            $this->{$relation}()->getForeignKeyName() :
            $this->{$relation}()->getForeignKey();

        if (!$this->joinAlreadyExists($relationTable)) {
            $this->sortQuery->join($relationTable, $modelTable . '.id', '=', $relationTable . '.' . $foreignKey);
        }

        $this->sortQuery->orderBy($relationTable . '.' . $field, $this->sortRequest->get($this->sortDirection));
    }

    /**
     * Sort model records using columns from the model's table itself.
     *
     * @return void
     */
    private function sortNormally()
    {
        $this->sortQuery->orderBy($this->sortRequest->get($this->sortField), $this->sortRequest->get($this->sortDirection));
    }

    /**
     * Verify if all sorting conditions are met.
     *
     * @return bool
     */
    private function isValidSort()
    {
        return $this->sortRequest->isMethod('get') && $this->sortRequest->has($this->sortField) && $this->sortRequest->has($this->sortDirection);
    }

    /**
     * Set the sort field if an App\Http\Sorts\Sort instance has been provided as a parameter for the sorted scope.
     *
     * @return void
     */
    private function setFieldToSortBy()
    {
        if ($this->sortInstance instanceof Sort) {
            $this->sortField = $this->sortInstance->field();
        }
    }

    /**
     * Set the sort direction if an App\Http\Sorts\Sort instance has been provided as a parameter for the sorted scope.
     *
     * @return void
     */
    private function setDirectionToSortIn()
    {
        if ($this->sortInstance instanceof Sort) {
            $this->sortDirection = $this->sortInstance->direction();
        }
    }

    /**
     * @return bool
     */
    private function shouldSortByRelation()
    {
        return str_contains($this->sortRequest->get($this->sortField), '.');
    }

    /**
     * Verify if the desired join exists already, possibly included by a global scope.
     *
     * @param string $table
     * @return bool
     */
    private function joinAlreadyExists($table)
    {
        return str_contains($this->sortQuery->toSql(), '`' . $table . '`');
    }

    /**
     * Verify if the direction provided in the request matches one of the directions from:
     * App\Http\Sorts\Sort::$directions.
     *
     * @throws SortException
     */
    private function checkSortingDirection()
    {
        if (!in_array(strtolower($this->sortRequest->get($this->sortDirection)), array_map('strtolower', Sort::$directions))) {
            throw new SortException(
                'Invalid sorting direction.' . PHP_EOL .
                'You provided the direction: "' . $this->sortRequest->get($this->sortDirection) . '".' . PHP_EOL .
                'Please provide one of these directions: ' . implode('|', Sort::$directions) . '.'
            );
        }
    }

    /**
     * Verify if the desired relation to sort by is one of: HasOne or BelongsTo.
     * Sorting by "many" relations or "morph" ones is not possible.
     *
     * @param string $relation
     * @throws SortException
     */
    private function checkRelationToSortBy($relation)
    {
        if (!($this->{$relation}() instanceof HasOne) && !($this->{$relation}() instanceof BelongsTo)) {
            throw new SortException(
                'You can only sort records by the following relations: HasOne, BelongsTo.' . PHP_EOL .
                'The relation "' . $relation . '" is of type ' . get_class($this->{$relation}()) . ' and cannot be sorted by.'
            );
        }
    }
}