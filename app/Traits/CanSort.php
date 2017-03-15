<?php

namespace App\Traits;

use App\Http\Sorts\Sort;
use App\Exceptions\SortException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait CanSort
{
    /**
     * The App\Http\Sorts\Sort instance.
     * This is used to get the sorting rules, just like a request.
     *
     * @var Sort
     */
    protected $sortInstance;

    /**
     * The query builder instance from the Sorted scope.
     *
     * @var Builder
     */
    protected $sortQuery;

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
     * @param Sort $sort
     */
    public function scopeSorted($query, Sort $sort = null)
    {
        $this->sortInstance = $sort;
        $this->sortQuery = $query;

        $this->setFieldToSortBy();
        $this->setDirectionToSortIn();

        if (request()->isMethod('get') && request()->has($this->sortField) && request()->has($this->sortDirection)) {
            $this->checkSortingDirection();

            switch ($direction = request($this->sortDirection)) {
                case Sort::DIRECTION_RANDOM:
                    $this->sortQuery->inRandomOrder();
                    break;
                default:
                    if ($this->shouldSortByRelation()) {
                        $this->sortByRelation();
                    } else {
                        $this->sortNormally();
                    }

                    break;
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
        $relation = explode('.', request($this->sortField))[0];
        $field = explode('.', request($this->sortField))[1];

        $this->checkRelationToSortBy($relation);

        $modelTable = $this->getTable();
        $relationTable = $this->{$relation}()->getModel()->getTable();
        $foreignKey = $this->{$relation}() instanceof HasOne ?
            $this->{$relation}()->getForeignKeyName() :
            $this->{$relation}()->getForeignKey();

        if (!$this->joinAlreadyExists($relationTable)) {
            $this->sortQuery->join($relationTable, $modelTable . '.id', '=', $relationTable . '.' . $foreignKey);
        }

        $this->sortQuery->orderBy($relationTable . '.' . $field, request($this->sortDirection));
    }

    /**
     * Sort model records using columns from the model's table itself.
     *
     * @return void
     */
    private function sortNormally()
    {
        $this->sortQuery->orderBy(request($this->sortField), request($this->sortDirection));
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
        return str_contains(request($this->sortField), '.');
    }

    /**
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
        if (!in_array(strtolower(request($this->sortDirection)), array_map('strtolower', Sort::$directions))) {
            throw new SortException(
                'Invalid sorting direction.' . PHP_EOL .
                'You provided the direction: "' . request($this->sortDirection) . '".' . PHP_EOL .
                'Please provide one of these directions: ' . implode('|', Sort::$directions) . '.'
            );
        }
    }

    /**
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