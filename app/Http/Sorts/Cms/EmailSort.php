<?php

namespace App\Http\Sorts\Cms;

use App\Http\Sorts\Sort;

class EmailSort extends Sort
{
    /**
     * Get the request field name to sort by.
     *
     * @return string
     */
    public function field()
    {
        return 'sort';
    }

    /**
     * Get the direction to sort by.
     *
     * @return array
     */
    public function direction()
    {
        return 'dir';
    }
}