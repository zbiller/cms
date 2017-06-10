<?php

namespace App\Traits;

use App\Http\Sorts\Sort;
use App\Exceptions\SortException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait IsSortable
{
    protected $sort = [
        /**
         * The query builder instance from the Sorted scope.
         *
         * @var Builder
         */
        'query' => null,

        /**
         * The request object passed from the controller applying the "filtered" scope on a model.
         *
         * @var Request
         */
        'request' => null,

        /**
         * The App\Http\Sorts\Sort instance.
         * This is used to get the sorting rules, just like a request.
         *
         * @var Sort
         */
        'instance' => null,

        /**
         * The field to sort by.
         *
         * @var string
         */
        'field' => Sort::DEFAULT_SORT_FIELD,

        /**
         * The direction to sort in.
         *
         * @var string
         */
        'direction' => Sort::DEFAULT_DIRECTION_FIELD,
    ];

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
        $this->sort['query'] = $query;
        $this->sort['request'] = $request;
        $this->sort['instance'] = $sort;

        $this->setFieldToSortBy();
        $this->setDirectionToSortIn();

        if ($this->isValidSort()) {
            $this->checkSortingDirection();

            if ($this->sort['request']->get($this->sort['direction']) == Sort::DIRECTION_RANDOM) {
                $this->sort['query']->inRandomOrder();
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
        list($relation, $field) = explode('.', $this->sort['request']->get($this->sort['field']));

        $this->checkRelationToSortBy($relation);

        $modelTable = $this->getTable();
        $relationTable = $this->{$relation}()->getModel()->getTable();
        $foreignKey = $this->{$relation}() instanceof HasOne ?
            $this->{$relation}()->getForeignKeyName() :
            $this->{$relation}()->getForeignKey();

        if (!$this->joinAlreadyExists($relationTable)) {
            $this->sort['query']->join($relationTable, $modelTable . '.id', '=', $relationTable . '.' . $foreignKey);
        }

        $this->sort['query']->orderBy($relationTable . '.' . $field, $this->sort['request']->get($this->sort['direction']));
    }

    /**
     * Sort model records using columns from the model's table itself.
     *
     * @return void
     */
    private function sortNormally()
    {
        $this->sort['query']->orderBy($this->sort['request']->get($this->sort['field']), $this->sort['request']->get($this->sort['direction']));
    }

    /**
     * Verify if all sorting conditions are met.
     *
     * @return bool
     */
    private function isValidSort()
    {
        return $this->sort['request']->isMethod('get') && $this->sort['request']->has($this->sort['field']) && $this->sort['request']->has($this->sort['direction']);
    }

    /**
     * Set the sort field if an App\Http\Sorts\Sort instance has been provided as a parameter for the sorted scope.
     *
     * @return void
     */
    private function setFieldToSortBy()
    {
        if ($this->sort['instance'] instanceof Sort) {
            $this->sort['field'] = $this->sort['instance']->field();
        }
    }

    /**
     * Set the sort direction if an App\Http\Sorts\Sort instance has been provided as a parameter for the sorted scope.
     *
     * @return void
     */
    private function setDirectionToSortIn()
    {
        if ($this->sort['instance'] instanceof Sort) {
            $this->sort['direction'] = $this->sort['instance']->direction();
        }
    }

    /**
     * @return bool
     */
    private function shouldSortByRelation()
    {
        return str_contains($this->sort['request']->get($this->sort['field']), '.');
    }

    /**
     * Verify if the desired join exists already, possibly included by a global scope.
     *
     * @param string $table
     * @return bool
     */
    private function joinAlreadyExists($table)
    {
        return str_contains($this->sort['query']->toSql(), '`' . $table . '`');
    }

    /**
     * Verify if the direction provided in the request matches one of the directions from:
     * App\Http\Sorts\Sort::$directions.
     *
     * @throws SortException
     */
    private function checkSortingDirection()
    {
        if (!in_array(strtolower($this->sort['request']->get($this->sort['direction'])), array_map('strtolower', Sort::$directions))) {
            throw new SortException(
                'Invalid sorting direction.' . PHP_EOL .
                'You provided the direction: "' . $this->sort['request']->get($this->sort['direction']) . '".' . PHP_EOL .
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