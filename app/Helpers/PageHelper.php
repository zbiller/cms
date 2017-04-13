<?php

namespace App\Helpers;

use App\Models\Cms\Page;

class PageHelper
{
    /**
     * Get a page instance by it's identifier.
     *
     * @param string $identifier
     * @return mixed
     */
    public function find($identifier)
    {
        return Page::withTrashed()->whereIdentifier($identifier)->first();
    }

    /**
     * Get all pages sorted by date descending.
     *
     * @return mixed
     */
    public function all()
    {
        return Page::defaultOrder()->get();
    }
}