<?php

if (!function_exists('form')) {
    /**
     * @return \Collective\Html\FormBuilder
     */
    function form()
    {
        return app(\Collective\Html\FormBuilder::class);
    }
}

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

if (!function_exists('button')) {
    /**
     * @return \App\Helpers\Button
     */
    function button()
    {
        return app(Button::class);
    }
}