<?php

if (!function_exists('form')) {
    /**
     * @return \App\Helpers\Form\Form
     */
    function form()
    {
        return app(\Collective\Html\FormBuilder::class);
    }
}

if (!function_exists('adminform')) {
    /**
     * @return \App\Helpers\Form\Admin
     */
    function adminform()
    {
        return app(AdminForm::class);
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
     * @return \App\Helpers\View\Pagination
     */
    function pagination()
    {
        return app(Pagination::class);
    }
}

if (!function_exists('button')) {
    /**
     * @return \App\Helpers\View\Button
     */
    function button()
    {
        return app(Button::class);
    }
}

if (!function_exists('flash')) {
    /**
     * @return \App\Helpers\Message\Flash
     */
    function flash()
    {
        return app(Flash::class);
    }
}