<?php

namespace App\Http\Filters\Admin;

use App\Http\Filters\Filter;

class AdminUserFilter extends Filter
{
    /**
     * Get the filters that apply to the request.
     *
     * @return array
     */
    public function filters()
    {
        return [
            'search' => 'operator:like|condition:or|columns:username,person.first_name,person.last_name,person.email,person.phone',
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