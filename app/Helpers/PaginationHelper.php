<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginationHelper
{
    /**
     * The pagination type to be rendered.
     * For now, only "default" and "admin" are available.
     * The render() method on this helper will try to display the view with the name of this property.
     *
     * @var string
     */
    protected $type = 'default';

    /**
     * Set the pagination type (view) to render.
     *
     * @param string|null $type
     */
    public function __construct($type = null)
    {
        if ($type) {
            $this->type = $type;
        }
    }

    /**
     * Display the pagination view helper.
     *
     * @param LengthAwarePaginator $items
     * @param array $data
     * @return string
     */
    public function render(LengthAwarePaginator $items, array $data = [])
    {
        return $items->links("helpers::pagination.{$this->type}", $data);
    }
}