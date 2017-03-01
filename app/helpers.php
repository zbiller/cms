<?php

if (!function_exists('menu')) {
    /**
     * @return \App\Helpers\Menu\Menu
     */
    function menu()
    {
        return app(Menu::class);
    }
}

if (!function_exists('pagination')) {
    /**
     * @return \App\Helpers\Pagination
     */
    function pagination()
    {
        return app(Pagination::class);
    }
}