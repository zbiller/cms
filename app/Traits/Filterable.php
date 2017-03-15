<?php

namespace App\Traits;

use App\Http\Filters\Filter;
use App\Exceptions\FilterException;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    /**
     * The App\Http\Filters\Filter instance.
     * This is used to get the filtering rules, just like a request.
     *
     * @var Filter
     */
    protected $filterInstance;

    /**
     * The query builder instance from the Filtered scope.
     *
     * @var Builder
     */
    protected $filterQuery;

    /**
     * The main where condition between entire request fields.
     * This can be either and | or.
     *
     * @var
     */
    protected $filterMorph;

    /**
     * The actual where method used to filter: {or}Where{Not}{Null}{In}{Between}{Date}.
     *
     * @var
     */
    protected $filterMethod;

    /**
     * The actual whereHas method used to filter by relations: {or}WhereHas.
     *
     * @var
     */
    protected $filterHaving;

    /**
     * The request field name from a GET request.
     *
     * @var
     */
    protected $filterField;

    /**
     * The actual value of the GET request field.
     *
     * @var
     */
    protected $filterValue;

    /**
     * Used for correctly filtering the records.
     * One of the values from App\Http\Filters\Filter::$operators.
     *
     * @var
     */
    protected $filterOperator;

    /**
     * Used for conditioning the filter on multiple columns.
     * One of the values from App\Http\Filters\Filter::$conditions.
     *
     * @var
     */
    protected $filterCondition;

    /**
     * One or more column names from the model's table.
     *
     * @var
     */
    protected $filterColumns;

    /**
     * The filter scope.
     * Should be called on the model when building the query.
     *
     * @param Builder $query
     * @param Filter $filter
     */
    public function scopeFiltered($query, Filter $filter)
    {
        $this->filterInstance = $filter;
        $this->filterQuery = $query;

        foreach ($this->filterInstance->filters() as $field => $options) {
            $this->filterField = $field;

            if (request()->isMethod('get') && $this->isValidFilterField()) {
                $this->setOperatorForFiltering($options);
                $this->setConditionToFilterBy($options);
                $this->setColumnsToFilterIn($options);
                $this->setMethodsOfFiltering();
                $this->setValueToFilterBy();

                $this->checkOperatorForFiltering();
                $this->checkConditionToFilterBy();
                $this->checkColumnsToFilterIn();

                $this->morph()->filter();
            }
        }
    }

    /**
     * Get the morph type defined in the App\Http\Filters\Filter corresponding class.
     * Build the general where method based on the morph.
     * Morph can only be "and" or "or".
     *
     * @return $this
     */
    protected function morph()
    {
        $this->filterMorph = 'where';

        if (strtolower($this->filterInstance->morph()) == 'or') {
            $this->filterMorph = 'or' . ucwords($this->filterMorph);
        }

        return $this;
    }

    /**
     * Filter the records.
     * The filtering takes into consideration fluid/descriptive where methods.
     * orWhere, whereNot, whereNull, whereIn, whereBetween, whereDate, etc.
     *
     * @return void
     */
    protected function filter()
    {
        $this->filterQuery->{$this->filterMorph}(function ($query) {
            foreach (explode(',', trim($this->filterColumns, ',')) as $column) {
                if ($this->shouldFilterByRelation($column)) {
                    $this->filterByRelation($query, $column);
                } else {
                    $this->filterNormally($query, $column);
                }
            }
        });
    }

    /**
     * Filter model records based on a relation defined.
     * Relation can be hasOne, hasMany, belongsTo or hasAndBelongsToMany.
     *
     * @param Builder $query
     * @param string $column
     * @return void
     */
    private function filterByRelation(Builder $query, $column)
    {
        $options = [];
        $arguments = explode('.', $column);
        $relation = camel_case($arguments[0]);
        $column = $arguments[1];

        $options[$relation][] = $column;

        foreach ($options as $relation => $columns) {
            $query->{$this->filterHaving}($relation, function ($q) use ($columns) {
                foreach ($columns as $index => $column) {
                    if ($index == 0) {
                        $method = lcfirst(str_replace('or', '', $this->filterMethod));
                    } else {
                        $method = $this->filterMethod;
                    }

                    $this->filterIndividually($q, $method, $column);
                }
            });
        }
    }

    /**
     * Filter model records using columns from the model's table itself.
     *
     * @param Builder $query
     * @param string $column
     * @return void
     */
    private function filterNormally(Builder $query, $column)
    {
        $this->filterIndividually($query, $this->filterMethod, $column);
    }

    /**
     * Abstraction of filtering to use in filtering by relations or normally.
     *
     * @param Builder $query
     * @param string $method
     * @param string $column
     * @return void
     */
    private function filterIndividually(Builder $query, $method, $column)
    {
        switch ($_method = strtolower($method)) {
            case str_contains($_method, Filter::OPERATOR_NULL):
                $query->{$method}($column);
                break;
            case str_contains($_method, Filter::OPERATOR_IN):
                $query->{$method}($column, $this->filterValue);
                break;
            case str_contains($_method, Filter::OPERATOR_BETWEEN):
                $query->{$method}($column, $this->filterValue);
                break;
            case str_contains($_method, Filter::OPERATOR_DATE):
                $operator = explode(' ', $this->filterOperator);

                $query->{$method}($column, (isset($operator[1]) ? $operator[1] : '='), $this->filterValue);
                break;
            default:
                $query->{$method}($column, $this->filterOperator, $this->filterValue);
                break;
        }
    }

    /**
     * @return bool
     */
    private function isValidFilterField()
    {
        return
            request()->has($this->filterField) ||
            in_array($this->filterField, Filter::$fields);
    }

    /**
     * @param string $column
     * @return bool
     */
    private function shouldFilterByRelation($column)
    {
        return str_contains($column, '.');
    }

    /**
     * Set the proper filtering method.
     * Also takes into consideration fluid/descriptive where methods.
     *
     * @return void
     */
    private function setMethodsOfFiltering()
    {
        $this->filterMethod = 'where';
        $this->filterHaving = 'whereHas';

        if ($this->filterCondition == Filter::CONDITION_OR) {
            $this->filterMethod = 'or' . ucwords($this->filterMethod);
            $this->filterHaving = 'or' . ucwords($this->filterHaving);
        }

        if (str_contains(strtolower($this->filterOperator), 'not')) {
            $this->filterMethod = $this->filterMethod . 'Not';
        }

        switch ($operator = strtolower($this->filterOperator)) {
            case str_contains($operator, 'null'):
                $this->filterMethod = $this->filterMethod . 'Null';
                break;
            case str_contains($operator, 'in'):
                $this->filterMethod = $this->filterMethod . 'In';
                break;
            case str_contains($operator, 'between'):
                $this->filterMethod = $this->filterMethod . 'Between';
                break;
            case str_contains($operator, 'date'):
                $this->filterMethod = $this->filterMethod . 'Date';
                break;
        }
    }

    /**
     * Set the value accordingly to the operator used.
     * Some of the operators require the value to be processed.
     *
     * @return void
     */
    private function setValueToFilterBy()
    {
        $this->filterValue = request($this->filterField);

        switch ($operator = strtolower($this->filterOperator)) {
            case str_contains($operator, Filter::OPERATOR_LIKE):
                $this->filterValue = "%" . $this->filterValue . "%";
                break;
            case str_contains($operator, Filter::OPERATOR_IN):
                $this->filterValue = (array)$this->filterValue;
                break;
            case str_contains($operator, Filter::OPERATOR_BETWEEN):
                $this->filterValue = (array)$this->filterValue;
                break;
        }
    }

    /**
     * Set the operator for filtering.
     * This is done based on the string defined in App\Http\Filters\Filter corresponding class.
     *
     * @param string $options
     */
    private function setOperatorForFiltering($options)
    {
        foreach (explode('|', $options) as $option) {
            $arguments = explode(':', $option);

            if (strtolower($arguments[0]) == 'operator') {
                $this->filterOperator = $arguments[1];
            }
        }
    }

    /**
     * Set the condition to filter by.
     * This is done based on the string defined in App\Http\Filters\Filter corresponding class.
     *
     * @param string $options
     */
    private function setConditionToFilterBy($options)
    {
        foreach (explode('|', $options) as $option) {
            $arguments = explode(':', $option);

            if (strtolower($arguments[0]) == 'condition') {
                $this->filterCondition = $arguments[1];
            }
        }
    }

    /**
     * Set the columns to filter in.
     * This is done based on the string defined in App\Http\Filters\Filter corresponding class.
     *
     * @param string $options
     */
    private function setColumnsToFilterIn($options)
    {
        foreach (explode('|', $options) as $option) {
            $arguments = explode(':', $option);

            if (strtolower($arguments[0]) == 'columns') {
                $this->filterColumns = $arguments[1];
            }
        }
    }

    /**
     * Verify if the operator has been properly set.
     * If not, throw a descriptive error for the developer to amend.
     *
     * @throws FilterException
     * @return void
     */
    private function checkOperatorForFiltering()
    {
        if (!isset($this->filterOperator) || !in_array(strtolower($this->filterOperator), array_map('strtolower', Filter::$operators))) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify an operator type.' . PHP_EOL .
                'Please specify an operator for "' . $this->filterField . '" in "' . get_class($this->filterInstance) . '".' . PHP_EOL .
                'Example: ---> "field" => "...operator:like..."'
            );
        }
    }

    /**
     * Verify if the condition has been properly set.
     * If not, throw a descriptive error for the developer to amend.
     *
     * @throws FilterException
     * @return void
     */
    private function checkConditionToFilterBy()
    {
        if (!isset($this->filterCondition) || !in_array(strtolower($this->filterCondition), array_map('strtolower', Filter::$conditions))) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify a condition type.' . PHP_EOL .
                'Please specify a condition for "' . $this->filterField . '" in "' . get_class($this->filterInstance) . '".' . PHP_EOL .
                'Example: ---> "field" => "...condition:or..."'
            );
        }
    }

    /**
     * Verify if the columns have been properly set.
     * If not, throw a descriptive error for the developer to amend.
     *
     * @throws FilterException
     * @return void
     */
    private function checkColumnsToFilterIn()
    {
        if (!isset($this->filterColumns) || empty($this->filterColumns)) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify the used columns.' . PHP_EOL .
                'Please specify the columns for "' . $this->filterField . '" in "' . get_class($this->filterInstance) . '"' . PHP_EOL .
                'Example: ---> "field" => "...columns:name,content..."'
            );
        }
    }
}