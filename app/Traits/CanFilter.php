<?php

namespace App\Traits;

use App\Http\Filters\Filter;
use App\Exceptions\FilterException;
use Illuminate\Database\Eloquent\Builder;

trait CanFilter
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
     * @var string
     */
    protected $filterWhere = null;

    /**
     * The filtering options.
     * Method: the actual where method used to filter: {or}Where{Not}{Null}{In}{Between}{Date}.
     * Field: the request field name from a GET request.
     * Value: the actual value of the GET request field.
     * Operator: used for correctly filtering the records. App\Http\Filters\Filter::$operators
     * Condition: used for conditioning the filter on multiple columns. App\Http\Filters\Filter::$conditions
     * Columns: one or many column names from the model's table.
     *
     * @var array
     */
    protected $filterOptions = [
        'method' => null,
        'field' => null,
        'value' => null,
        'operator' => null,
        'condition' => null,
        'columns' => null,
    ];

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
            $this->filterOptions['field'] = $field;

            $this->setOperatorConditionAndColumnsForFiltering($options);
            $this->checkOperatorConditionAndColumnsOfFiltering();

            if (
                request()->isMethod('get') &&
                (
                    request()->has($this->filterOptions['field']) ||
                    in_array($this->filterOptions['field'], Filter::$fields)
                )
            ) {
                $this->setMethodOfFiltering();
                $this->setValueToFilterBy();

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
        switch ($this->filterInstance->morph()) {
            case 'and':
                $this->filterWhere = 'where';
                break;
            case 'or':
                $this->filterWhere = 'orWhere';
                break;
            default:
                $this->filterWhere = 'where';
                break;
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
        $this->filterQuery->{$this->filterWhere}(function ($query) {
            foreach (explode(',', $this->filterOptions['columns']) as $column) {
                switch ($method = $this->filterOptions['method']) {
                    case str_contains(strtolower($method), Filter::OPERATOR_NULL):
                        $query->{$method}($column);
                        break;
                    case str_contains(strtolower($method), Filter::OPERATOR_IN):
                        $query->{$method}($column, $this->filterOptions['value']);
                        break;
                    case str_contains(strtolower($method), Filter::OPERATOR_BETWEEN):
                        $query->{$method}($column, $this->filterOptions['value']);
                        break;
                    case str_contains(strtolower($method), Filter::OPERATOR_DATE):
                        $operator = explode(' ', $this->filterOptions['operator']);
                        $query->{$method}($column, (isset($operator[1]) ? $operator[1] : '='), $this->filterOptions['value']);
                        break;
                    default:
                        $query->{$method}($column, $this->filterOptions['operator'], $this->filterOptions['value']);
                        break;
                }
            }
        });
    }

    /**
     * Set the proper filtering method.
     * Also takes into consideration fluid/descriptive where methods.
     *
     * @return void
     */
    private function setMethodOfFiltering()
    {
        $this->filterOptions['method'] = 'where';

        if ($this->filterOptions['condition'] == Filter::CONDITION_OR) {
            $this->filterOptions['method'] = 'or' . ucwords($this->filterOptions['method']);
        }

        if (str_contains(strtolower($this->filterOptions['operator']), 'not')) {
            $this->filterOptions['method'] = $this->filterOptions['method'] . 'Not';
        }

        switch ($operator = strtolower($this->filterOptions['operator'])) {
            case str_contains($operator, 'null'):
                $this->filterOptions['method'] = $this->filterOptions['method'] . 'Null';
                break;
            case str_contains($operator, 'in'):
                $this->filterOptions['method'] = $this->filterOptions['method'] . 'In';
                break;
            case str_contains($operator, 'between'):
                $this->filterOptions['method'] = $this->filterOptions['method'] . 'Between';
                break;
            case str_contains($operator, 'date'):
                $this->filterOptions['method'] = $this->filterOptions['method'] . 'Date';
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
        $this->filterOptions['value'] = request($this->filterOptions['field']);

        switch ($operator = strtolower($this->filterOptions['operator'])) {
            case str_contains($operator, Filter::OPERATOR_LIKE):
                $this->filterOptions['value'] = "%" . $this->filterOptions['value'] . "%";
                break;
            case str_contains($operator, Filter::OPERATOR_IN):
                $this->filterOptions['value'] = (array)$this->filterOptions['value'];
                break;
            case str_contains($operator, Filter::OPERATOR_BETWEEN):
                $this->filterOptions['value'] = (array)$this->filterOptions['value'];
                break;
        }
    }

    /**
     * Set the "operator", "condition" and "columns".
     * This is done based on the string defined in App\Http\Filters\Filter corresponding class.
     *
     * @param string $options
     * @return void
     */
    private function setOperatorConditionAndColumnsForFiltering($options)
    {
        foreach (explode('|', $options) as $option) {
            $this->filterOptions[explode(':', $option)[0]] = explode(':', $option)[1];
        }

        if ($this->filterOptions['condition'] === null) {
            $this->filterOptions['condition'] = Filter::CONDITION_AND;
        }
    }

    /**
     * Verify if "operator", "condition" and "columns" have been properly set.
     * If not, throw a descriptive error for the developer to amend.
     *
     * @throws FilterException
     * @return void
     */
    private function checkOperatorConditionAndColumnsOfFiltering()
    {
        if (!isset($this->filterOptions['operator']) || !in_array(strtolower($this->filterOptions['operator']), array_map('strtolower', Filter::$operators))) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify an operator type.' . PHP_EOL .
                'Please specify an operator for "' . $this->filterOptions['field'] . '" in "' . get_class($this->filterInstance) . '".' . PHP_EOL .
                'Example: ---> "field" => "...operator:like..."'
            );
        }

        if (!isset($this->filterOptions['condition']) || !in_array(strtolower($this->filterOptions['condition']), array_map('strtolower', Filter::$conditions))) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify a condition type.' . PHP_EOL .
                'Please specify a condition for "' . $this->filterOptions['field'] . '" in "' . get_class($this->filterInstance) . '".' . PHP_EOL .
                'Example: ---> "field" => "...condition:or..."'
            );
        }

        if (!isset($this->filterOptions['columns']) || empty($this->filterOptions['columns'])) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify the used columns.' . PHP_EOL .
                'Please specify the columns for "' . $this->filterOptions['field'] . '" in "' . get_class($this->filterInstance) . '"' . PHP_EOL .
                'Example: ---> "field" => "...columns:name,content..."'
            );
        }
    }
}