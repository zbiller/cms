<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;

class Pagination
{
    /**
     * @param LengthAwarePaginator $items
     * @param string $view
     * @param array $data
     * @return string
     */
    public function render(LengthAwarePaginator $items, $view, array $data = [])
    {
        return $items->links('helpers::pagination.' . $view, $data);
    }
}