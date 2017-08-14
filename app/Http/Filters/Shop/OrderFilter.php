<?php

namespace App\Http\Filters\Shop;

use App\Http\Filters\Filter;

class OrderFilter extends Filter
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
            'search' => 'operator:like|condition:or|columns:identifier,currency,customer,addresses',
            'total' => 'operator:between|condition:or|columns:grand_total',
            'payment' => 'operator:=|condition:or|columns:payment',
            'shipping' => 'operator:=|condition:or|columns:shipping',
            'status' => 'operator:=|condition:or|columns:status',
            'viewed' => 'operator:=|condition:or|columns:viewed',
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