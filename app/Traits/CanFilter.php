<?php

namespace App\Traits;

use Closure;
use App\Http\Filters\Filter;
use App\Exceptions\FilterException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait CanFilter
{
    /**
     * The query builder instance from the Filtered scope.
     *
     * @var Builder
     */
    protected $filterQuery;

    /**
     * The App\Http\Filters\Filter instance.
     * This is used to get the filtering rules, just like a request.
     *
     * @var Filter
     */
    protected $filterInstance;

    /**
     * The request object passed from the controller applying the "filtered" scope on a model.
     *
     * @var Request
     */
    protected $filterRequest;

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
     * @param Request $request
     * @param Filter $filter
     * @throws FilterException
     */
    public function scopeFiltered($query, Request $request, Filter $filter)
    {
        $this->filterQuery = $query;
        $this->filterRequest = $request;
        $this->filterInstance = $filter;

        foreach ($this->filterInstance->filters() as $field => $options) {
            $this->filterField = $field;

            if ($this->isValidFilter()) {
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
        $relation = camel_case(explode('.', $column)[0]);
        $options[$relation][] = explode('.', $column)[1];

        foreach ($options as $relation => $columns) {
            $query->{$this->filterHaving}($relation, function ($q) use ($columns) {
                foreach ($columns as $index => $column) {
                    $method = $index == 0 ? lcfirst(str_replace('or', '', $this->filterMethod)) : $this->filterMethod;

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
     * Verify if all filtering conditions are met.
     *
     * @return bool
     */
    private function isValidFilter()
    {
        return $this->filterRequest->isMethod('get') && $this->isValidFilterField() && !$this->isNullFilterField();
    }

    /**
     * Verify if the request field has a valid value.
     *
     * @return bool
     */
    private function isValidFilterField()
    {
        return $this->filterRequest->has($this->filterField) || in_array($this->filterField, Filter::$fields);
    }

    /**
     * Verify if the entire request array consists only of null values or not.
     *
     * @return bool
     */
    private function isNullFilterField()
    {
        if (is_array($this->filterRequest->get($this->filterField))) {
            $count = 0;

            foreach ($this->filterRequest->get($this->filterField) as $value) {
                if ($value === null) {
                    $count++;
                }
            }

            return $count == count($this->filterRequest->get($this->filterField));
        } else {
            return is_null($this->filterRequest->get($this->filterField));
        }
    }

    /**
     * Determine if filtering should focus on a subsequent relationship.
     * The convention here is to use dot ".", separating the table from column.
     *
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
     * Also, this method handles the modifiers() method if defined on the filter class.
     *
     * @return void
     */
    private function setValueToFilterBy()
    {
        if (
            method_exists($this->filterInstance, 'modifiers') &&
            array_key_exists($this->filterField, $this->filterInstance->modifiers())
        ) {
            foreach ($this->filterInstance->modifiers() as $field => $value) {
                if ($field == $this->filterField) {
                    $this->filterValue = $value instanceof Closure ? $value(null) : $value;
                    break;
                }
            }
        } else {
            $this->filterValue = $this->filterRequest->get($this->filterField);
        }

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