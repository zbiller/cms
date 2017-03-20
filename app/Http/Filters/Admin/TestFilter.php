<?php

namespace App\Http\Filters\Admin;

use App\Http\Filters\Filter;

class TestFilter extends Filter
{
    /**
     * Get the filters that apply to the request.
     *
     * @return array
     */
    public function filters()
    {
        return [
            'habtm' => 'operator:in|condition:or|columns:test_habtm.test_id',
            'search' => 'operator:like|condition:or|columns:name,content',
            'type' => 'operator:=|condition:or|columns:type',
            'created_at' => 'operator:date|condition:and|columns:created_at,updated_at',
        ];
    }

    /**
     * Get the main where condition between entire request fields.
     *
     * @return string
     */
    public function morph()
    {
        return 'and';
    }
}