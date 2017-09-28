<?php

namespace App\Http\Filters\Seo;

use App\Http\Filters\Filter;

class RedirectFilter extends Filter
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
            'search' => 'operator:like|condition:or|columns:old_url,new_url',
            'status' => 'operator:=|condition:or|columns:status',
            'start_date' => 'operator:date >=|condition:or|columns:date',
            'end_date' => 'operator:date <=|condition:or|columns:date',
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