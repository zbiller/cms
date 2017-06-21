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
     * Get all root pages.
     * Pages that do not have a parent.
     *
     * @return mixed
     */
    public function roots()
    {
        return Page::withTrashed()->whereIsRoot()->get();
    }

    /**
     * Get all children pages for a specified parent
     *
     * @param int $parent
     * @return mixed
     */
    public function children($parent)
    {
        return Page::withTrashed()->whereDescendantOf($parent)->get();
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