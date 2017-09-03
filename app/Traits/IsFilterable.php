<?php

namespace App\Traits;

use App\Exceptions\FilterException;
use App\Http\Filters\Filter;
use BadMethodCallException;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait IsFilterable
{
    protected $filter = [
        /**
         * The query builder instance from the Filtered scope.
         *
         * @var Builder
         */
        'query' => null,

        /**
         * The App\Http\Filters\Filter instance.
         * This is used to get the filtering rules, just like a request.
         *
         * @var Filter
         */
        'instance' => null,

        /**
         * The request object passed from the controller applying the "filtered" scope on a model.
         *
         * @var Request
         */
        'request' => null,

        /**
         * The main where condition between entire request fields.
         * This can be either and | or.
         *
         * @var
         */
        'morph' => null,

        /**
         * The actual where method used to filter: {or}Where{Not}{Null}{In}{Between}{Date}.
         *
         * @var
         */
        'method' => null,

        /**
         * The actual whereHas method used to filter by relations: {or}WhereHas.
         *
         * @var
         */
        'having' => null,

        /**
         * The request field name from a GET request.
         *
         * @var
         */
        'field' => null,

        /**
         * The actual value of the GET request field.
         *
         * @var
         */
        'value' => null,

        /**
         * Used for correctly filtering the records.
         * One of the values from App\Http\Filters\Filter::$operators.
         *
         * @var
         */
        'operator' => null,

        /**
         * Used for conditioning the filter on multiple columns.
         * One of the values from App\Http\Filters\Filter::$conditions.
         *
         * @var
         */
        'condition' => null,

        /**
         * One or more column names from the model's table.
         *
         * @var
         */
        'columns' => null,
    ];

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
        $this->filter['query'] = $query;
        $this->filter['request'] = $request;
        $this->filter['instance'] = $filter;

        foreach ($this->filter['instance']->filters() as $field => $options) {
            $this->filter['field'] = $field;

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
        $this->filter['morph'] = 'where';

        if (strtolower($this->filter['instance']->morph()) == 'or') {
            $this->filter['morph'] = 'or' . ucwords($this->filter['morph']);
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
        $this->filter['query']->{$this->filter['morph']}(function ($query) {
            foreach (explode(',', trim($this->filter['columns'], ',')) as $column) {
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
            try {
                $query->{$this->filter['having']}($relation, function ($q) use ($columns) {
                    foreach ($columns as $index => $column) {
                        $method = $index == 0 ? lcfirst(str_replace('or', '', $this->filter['method'])) : $this->filter['method'];

                        $this->filterIndividually($q, $method, $column);
                    }
                });
            } catch (BadMethodCallException $e) {
                $this->filterIndividually($query, $this->filter['method'], $column);
            }
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
        $this->filterIndividually($query, $this->filter['method'], $column);
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
                $query->{$method}($column, $this->filter['value']);
                break;
            case str_contains($_method, Filter::OPERATOR_BETWEEN):
                $query->{$method}($column, $this->filter['value']);
                break;
            case str_contains($_method, Filter::OPERATOR_DATE):
                $operator = explode(' ', $this->filter['operator']);
                $query->{$method}($column, ($operator[1] ?? '='), $this->filter['value']);
                break;
            default:
                $query->{$method}($column, $this->filter['operator'], $this->filter['value']);
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
        return $this->filter['request']->isMethod('get') && $this->isValidFilterField() && !$this->isNullFilterField();
    }

    /**
     * Verify if the request field has a valid value.
     *
     * @return bool
     */
    private function isValidFilterField()
    {
        return $this->filter['request']->has($this->filter['field']) || in_array($this->filter['field'], Filter::$fields);
    }

    /**
     * Verify if the entire request array consists only of null values or not.
     *
     * @return bool
     */
    private function isNullFilterField()
    {
        if (is_array($this->filter['request']->get($this->filter['field']))) {
            $count = 0;

            foreach ($this->filter['request']->get($this->filter['field']) as $value) {
                if ($value === null) {
                    $count++;
                }
            }

            return $count == count($this->filter['request']->get($this->filter['field']));
        } else {
            return is_null($this->filter['request']->get($this->filter['field']));
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
        $this->filter['method'] = 'where';
        $this->filter['having'] = 'whereHas';

        if ($this->filter['condition'] == Filter::CONDITION_OR) {
            $this->filter['method'] = 'or' . ucwords($this->filter['method']);
            $this->filter['having'] = 'or' . ucwords($this->filter['having']);
        }

        if (str_contains(strtolower($this->filter['operator']), 'not')) {
            $this->filter['method'] = $this->filter['method'] . 'Not';
        }

        switch ($operator = strtolower($this->filter['operator'])) {
            case str_contains($operator, 'null'):
                $this->filter['method'] = $this->filter['method'] . 'Null';
                break;
            case str_contains($operator, 'in'):
                $this->filter['method'] = $this->filter['method'] . 'In';
                break;
            case str_contains($operator, 'between'):
                $this->filter['method'] = $this->filter['method'] . 'Between';
                break;
            case str_contains($operator, 'date'):
                $this->filter['method'] = $this->filter['method'] . 'Date';
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
            method_exists($this->filter['instance'], 'modifiers') &&
            array_key_exists($this->filter['field'], $this->filter['instance']->modifiers())
        ) {
            foreach ($this->filter['instance']->modifiers() as $field => $value) {
                if ($field == $this->filter['field']) {
                    $this->filter['value'] = $value instanceof Closure ? $value(null) : $value;
                    break;
                }
            }
        } else {
            $this->filter['value'] = $this->filter['request']->get($this->filter['field']);
        }

        switch ($operator = strtolower($this->filter['operator'])) {
            case str_contains($operator, Filter::OPERATOR_LIKE):
                $this->filter['value'] = "%" . $this->filter['value'] . "%";
                break;
            case str_contains($operator, Filter::OPERATOR_IN):
                $this->filter['value'] = (array)$this->filter['value'];
                break;
            case str_contains($operator, Filter::OPERATOR_BETWEEN):
                $this->filter['value'] = (array)$this->filter['value'];
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
                $this->filter['operator'] = $arguments[1];
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
                $this->filter['condition'] = $arguments[1];
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
                $this->filter['columns'] = $arguments[1];
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
        if (!isset($this->filter['operator']) || !in_array(strtolower($this->filter['operator']), array_map('strtolower', Filter::$operators))) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify an operator type.' . PHP_EOL .
                'Please specify an operator for "' . $this->filter['field'] . '" in "' . get_class($this->filter['instance']) . '".' . PHP_EOL .
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
        if (!isset($this->filter['condition']) || !in_array(strtolower($this->filter['condition']), array_map('strtolower', Filter::$conditions))) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify a condition type.' . PHP_EOL .
                'Please specify a condition for "' . $this->filter['field'] . '" in "' . get_class($this->filter['instance']) . '".' . PHP_EOL .
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
        if (!isset($this->filter['columns']) || empty($this->filter['columns'])) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify the used columns.' . PHP_EOL .
                'Please specify the columns for "' . $this->filter['field'] . '" in "' . get_class($this->filter['instance']) . '"' . PHP_EOL .
                'Example: ---> "field" => "...columns:name,content..."'
            );
        }
    }
}