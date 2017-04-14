<?php

namespace App\Helpers;

use App\Models\Cms\Page;

class PageHelper
{
    /**
     * Get a page by it's identifier.
     *
     * @param string $identifier
     * @return mixed
     */
    public function find($identifier)
    {
        return Page::withTrashed()->whereIdentifier($identifier)->first();
    }

    /**
     * Get all pages.
     *
     * @return mixed
     */
    public function all()
    {
        return Page::withTrashed()->get();
    }

    /**
     * Get a new builder instance with global scopes applied.
     *
     * @return mixed
     */
    public function query()
    {
        return app(Page::class)->newQuery();
    }
}