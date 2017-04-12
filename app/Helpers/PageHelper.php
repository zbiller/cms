<?php

namespace App\Helpers;

use App\Models\Cms\Page;

class PageHelper
{
    /**
     * Get a page instance by it's identifier.
     *
     * @param string $identifier
     */
    public function __construct($identifier)
    {
        return Page::whereIdentifier($identifier)->first();
    }
}