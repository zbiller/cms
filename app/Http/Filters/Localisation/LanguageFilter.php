<?php

namespace App\Http\Filters\Localisation;

use App\Http\Filters\Filter;

class LanguageFilter extends Filter
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
            'search' => 'operator:like|condition:or|columns:name,code',
            'default' => 'operator:=|condition:or|columns:default',
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