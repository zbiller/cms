<?php

namespace App\Http\Filters;

class CarFilter extends Filter
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
            'search' => 'operator:like|condition:or|columns:name,slug',
            'owner' => 'operator:=|condition:or|columns:owner_id',
            'brand' => 'operator:=|condition:or|columns:brand_id',
            'book' => 'operator:in|condition:or|columns:book.id',
            'mechanics' => 'operator:in|condition:or|columns:mechanics.mechanic_id',
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