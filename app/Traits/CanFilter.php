<?php

namespace App\Traits;

use App\Http\Filters\Filter;
use App\Exceptions\FilterException;
use Illuminate\Database\Eloquent\Builder;

trait CanFilter
{
    /**
     * The query builder instance from the Filtered scope.
     *
     * @var Builder
     */
    protected $_query;

    /**
     * The App\Http\Filters\Filter instance.
     * This is used to get the filtering rules, just like a request.
     *
     * @var Filter
     */
    protected $_filter;

    /**
     * The main where condition between entire request fields.
     * This can be either and | or.
     *
     * @var string
     */
    protected $_where = null;

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
    protected $_options = [
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
        $this->_query = $query;
        $this->_filter = $filter;

        foreach ($this->_filter->filters() as $field => $options) {
            $this->_options['field'] = $field;

            $this->parseOperatorConditionAndColumns($options);
            $this->checkOperatorConditionAndColumns();

            $this->apply();
        }
    }

    /**
     * Verifies if the filter conditions are met. (request method is get, field exists)
     * Sets up the filtering process.
     *
     * @throws FilterException
     * @return void
     */
    protected function apply()
    {
        if (
            request()->isMethod('get') &&
            request()->has($this->_options['field']) ||
            in_array($this->_options['field'], Filter::$fields)
        ) {
            $this->parseMethod();
            $this->parseValue();

            $this->morph()->filter();
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
        switch ($this->_filter->morph()) {
            case 'and':
                $this->_where = 'where';
                break;
            case 'or':
                $this->_where = 'orWhere';
                break;
            default:
                $this->_where = 'where';
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
        $this->_query->{$this->_where}(function ($query) {
            foreach (explode(',', $this->_options['columns']) as $column) {

                switch ($method = strtolower($this->_options['method'])) {
                    case str_contains($method, Filter::OPERATOR_NULL):
                        $query->{$this->_options['method']}($column);
                        break;
                    case str_contains($method, Filter::OPERATOR_IN):
                        $query->{$this->_options['method']}($column, $this->_options['value']);
                        break;
                    case str_contains($method, Filter::OPERATOR_BETWEEN):
                        $query->{$this->_options['method']}($column, $this->_options['value']);
                        break;
                    case str_contains($method, Filter::OPERATOR_DATE):
                        $operator = explode(' ', $this->_options['operator']);
                        $query->{$this->_options['method']}($column, (isset($operator[1]) ? $operator[1] : '='), $this->_options['value']);
                        break;
                    default:
                        $query->{$this->_options['method']}($column, $this->_options['operator'], $this->_options['value']);
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
    private function parseMethod()
    {
        $this->_options['method'] = 'where';

        if ($this->_options['condition'] == Filter::CONDITION_OR) {
            $this->_options['method'] = 'or' . ucwords($this->_options['method']);
        }

        if (str_contains(strtolower($this->_options['operator']), 'not')) {
            $this->_options['method'] = $this->_options['method'] . 'Not';
        }

        switch ($operator = strtolower($this->_options['operator'])) {
            case str_contains($operator, 'null'):
                $this->_options['method'] = $this->_options['method'] . 'Null';
                break;
            case str_contains($operator, 'in'):
                $this->_options['method'] = $this->_options['method'] . 'In';
                break;
            case str_contains($operator, 'between'):
                $this->_options['method'] = $this->_options['method'] . 'Between';
                break;
            case str_contains($operator, 'date'):
                $this->_options['method'] = $this->_options['method'] . 'Date';
                break;
        }
    }

    /**
     * Set the value accordingly to the operator used.
     * Some of the operators require the value to be processed.
     *
     * @return void
     */
    private function parseValue()
    {
        $this->_options['value'] = request()->get($this->_options['field']);

        switch ($operator = strtolower($this->_options['operator'])) {
            case str_contains($operator, Filter::OPERATOR_LIKE):
                $this->_options['value'] = "%" . $this->_options['value'] . "%";
                break;
            case str_contains($operator, Filter::OPERATOR_IN):
                $this->_options['value'] = (array)$this->_options['value'];
                break;
            case str_contains($operator, Filter::OPERATOR_BETWEEN):
                $this->_options['value'] = (array)$this->_options['value'];
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
    private function parseOperatorConditionAndColumns($options)
    {
        foreach (explode('|', $options) as $option) {
            $this->_options[explode(':', $option)[0]] = explode(':', $option)[1];
        }

        if ($this->_options['condition'] === null) {
            $this->_options['condition'] = Filter::CONDITION_AND;
        }
    }

    /**
     * Verify if "operator", "condition" and "columns" have been properly set.
     * If not, throw a descriptive error for the developer to amend.
     *
     * @throws FilterException
     * @return void
     */
    private function checkOperatorConditionAndColumns()
    {
        if (!isset($this->_options['operator']) || !in_array(strtolower($this->_options['operator']), array_map('strtolower', Filter::$operators))) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify an operator type.' . PHP_EOL .
                'Please specify an operator for "' . $this->_options['field'] . '" in "' . get_class($this->_filter) . '".' . PHP_EOL .
                'Example: ---> "field" => "...operator:like..."'
            );
        }

        if (!isset($this->_options['condition']) || !in_array(strtolower($this->_options['condition']), array_map('strtolower', Filter::$conditions))) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify a condition type.' . PHP_EOL .
                'Please specify a condition for "' . $this->_options['field'] . '" in "' . get_class($this->_filter) . '".' . PHP_EOL .
                'Example: ---> "field" => "...condition:or..."'
            );
        }

        if (!isset($this->_options['columns']) || empty($this->_options['columns'])) {
            throw new FilterException(
                'For each request field declared as filterable, you must specify the used columns.' . PHP_EOL .
                'Please specify the columns for "' . $this->_options['field'] . '" in "' . get_class($this->_filter) . '"' . PHP_EOL .
                'Example: ---> "field" => "...columns:name,content..."'
            );
        }
    }
}