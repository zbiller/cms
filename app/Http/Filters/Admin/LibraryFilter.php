<?php

namespace App\Http\Filters\Admin;

use App\Http\Filters\Filter;

class LibraryFilter extends Filter
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
            'search' => 'operator:like|condition:or|columns:original_name,full_path',
            'type' => 'operator:=|condition:or|columns:type',
            'size' => 'operator:between|condition:or|columns:size',
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
        return [
            'size' => function ($modified) {
                foreach (request('size') as $size) {
                    $modified[] = $size * pow(1024, 2);
                }

                return $modified;
            },
        ];
    }
}