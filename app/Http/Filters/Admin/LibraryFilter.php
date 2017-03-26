<?php

namespace App\Http\Filters\Admin;

use App\Http\Filters\Filter;

class LibraryFilter extends Filter
{
    /**
     * Get the filters that apply to the request.
     *
     * @return array
     */
    public function filters()
    {
        return [
            'search' => 'operator:like|condition:or|columns:original_name,full_path',
            'type' => 'operator:=|condition:or|columns:type',
            'size' => 'operator:between|condition:or|columns:size',
            'start_date' => 'operator:date >=|condition:or|columns:created_at',
            'end_date' => 'operator:date <=|condition:or|columns:created_at',
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