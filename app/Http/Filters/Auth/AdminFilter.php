<?php

namespace App\Http\Filters\Auth;

use App\Http\Filters\Filter;

class AdminFilter extends Filter
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
            'search' => 'operator:like|condition:or|columns:username,person.first_name,person.last_name,person.email,person.phone',
            'role' => 'operator:=|condition:or|columns:roles.role_id',
            'start_date' => 'operator:date >=|condition:or|columns:users.created_at',
            'end_date' => 'operator:date <=|condition:or|columns:users.created_at',
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