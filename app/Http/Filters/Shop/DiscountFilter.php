<?php

namespace App\Http\Filters\Shop;

use App\Http\Filters\Filter;

class DiscountFilter extends Filter
{
    /**
     * Get the main where condition between entire request fields.
     *
     * @return string
     */
    public function morph()
    {
        return 'and';
    }

    /**
     * Get the filters that apply to the request.
     *
     * @return array
     */
    public function filters()
    {
        return [
            'search' => 'operator:like|condition:or|columns:name',
            'type' => 'operator:=|condition:or|columns:type',
            'for' => 'operator:=|condition:or|columns:for',
            'active' => 'operator:=|condition:or|columns:active',
            'start_date' => 'operator:date >=|condition:or|columns:created_at',
            'end_date' => 'operator:date <=|condition:or|columns:created_at',
        ];
    }

    /**
     * Get the modified value of a request filter field.
     *
     * @return array
     */
    public function modifiers()
    {
        return [];
    }
}