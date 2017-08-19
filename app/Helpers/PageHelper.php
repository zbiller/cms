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
        return Page::withTrashed()->defaultOrder()->get();
    }

    /**
     * Get all root pages.
     * Pages that do not have a parent.
     *
     * @return mixed
     */
    public function roots()
    {
        return Page::withTrashed()->whereIsRoot()->defaultOrder()->get();
    }

    /**
     * Get all children pages for a specified parent
     *
     * @param int $parent
     * @return mixed
     */
    public function children($parent)
    {
        return Page::withTrashed()->whereDescendantOf($parent)->defaultOrder()->get();
    }

    /**
     * Build the canonical tag for a page if necessary.
     *
     * @param Page $page
     * @return string
     */
    public function canonical(Page $page)
    {
        if ($page->canonical) {
            return '<link rel="canonical" href="' . url($page->canonical) . '">';
        }
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