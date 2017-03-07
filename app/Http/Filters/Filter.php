<?php

namespace App\Http\Filters;

abstract class Filter
{
    /**
     * Use this when building a filter that isn't constrained by a request field.
     *
     * @const
     */
    const FIELD_ANY = '*';

    /**
     * Available conditions for the filter.
     *
     * @const
     */
    const CONDITION_AND = 'and';
    const CONDITION_OR = 'or';

    /**
     * Available operators for the filter.
     *
     * @const
     */
    const OPERATOR_EQUAL = '=';
    const OPERATOR_NOT_EQUAL = '!=';
    const OPERATOR_SMALLER = '<';
    const OPERATOR_BIGGER = '>';
    const OPERATOR_SMALLER_OR_EQUAL = '<=';
    const OPERATOR_BIGGER_OR_EQUAL = '>=';
    const OPERATOR_NULL = 'null';
    const OPERATOR_NOT_NULL = 'not null';
    const OPERATOR_LIKE = 'like';
    const OPERATOR_NOT_LIKE = 'not like';
    const OPERATOR_IN = 'in';
    const OPERATOR_NOT_IN = 'not in';
    const OPERATOR_BETWEEN = 'between';
    const OPERATOR_NOT_BETWEEN = 'not between';
    const OPERATOR_DATE = 'date';
    const OPERATOR_DATE_EQUAL = 'date =';
    const OPERATOR_DATE_NOT_EQUAL = 'date !=';
    const OPERATOR_DATE_SMALLER = 'date <';
    const OPERATOR_DATE_BIGGER = 'date >';
    const OPERATOR_DATE_SMALLER_OR_EQUAL = 'date <=';
    const OPERATOR_DATE_BIGGER_OR_EQUAL = 'date >=';

    /**
     * List of un-constrained fields.
     *
     * @var array
     */
    public static $fields = [
        self::FIELD_ANY,
    ];

    /**
     * List of filter conditions.
     *
     * @var array
     */
    public static $conditions = [
        self::CONDITION_AND,
        self::CONDITION_OR,
    ];

    /**
     * List of filter operators.
     *
     * @var array
     */
    public static $operators = [
        self::OPERATOR_EQUAL,
        self::OPERATOR_NOT_EQUAL,
        self::OPERATOR_SMALLER,
        self::OPERATOR_BIGGER,
        self::OPERATOR_SMALLER_OR_EQUAL,
        self::OPERATOR_BIGGER_OR_EQUAL,
        self::OPERATOR_NULL,
        self::OPERATOR_NOT_NULL,
        self::OPERATOR_IN,
        self::OPERATOR_NOT_IN,
        self::OPERATOR_LIKE,
        self::OPERATOR_NOT_LIKE,
        self::OPERATOR_BETWEEN,
        self::OPERATOR_NOT_BETWEEN,
        self::OPERATOR_DATE,
        self::OPERATOR_DATE_EQUAL,
        self::OPERATOR_DATE_NOT_EQUAL,
        self::OPERATOR_DATE_SMALLER,
        self::OPERATOR_DATE_BIGGER,
        self::OPERATOR_DATE_SMALLER_OR_EQUAL,
        self::OPERATOR_DATE_BIGGER_OR_EQUAL,
    ];

    /**
     * Get the main where condition between entire request fields.
     * This method should be implemented in this class' children.
     *
     * @return string
     */
    abstract public function morph();

    /**
     * Get the filters that apply to the request.
     * This method should be implemented in this class' children.
     *
     * @return array
     */
    abstract public function filters();
}