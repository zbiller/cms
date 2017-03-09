<?php

namespace App\Traits;

use App\Http\Sorts\Sort;
use App\Exceptions\SortException;
use Illuminate\Database\Eloquent\Builder;

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
                    $this->sortQuery->orderBy(request($this->sortField), request($this->sortDirection));
                    break;
            }
        }
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
}