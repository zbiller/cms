<?php

if (!function_exists('menu')) {
    /**
     * @return \App\Helpers\Menu
     */
    function menu()
    {
        return app(Menu::class);
    }
}