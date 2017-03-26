<?php

if (!function_exists('upload')) {
    /**
     * @param string $file
     * @return \App\Helpers\UploadHelper
     */
    function upload($file)
    {
        return new \App\Helpers\UploadHelper($file);
    }
}

if (!function_exists('form')) {
    /**
     * @return \Collective\Html\FormBuilder
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

if (!function_exists('validation')) {
    /**
     * @return \App\Helpers\View\Validation
     */
    function validation()
    {
        return app(Validation::class);
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

if (!function_exists('button')) {
    /**
     * @return \App\Helpers\View\Button
     */
    function button()
    {
        return app(Button::class);
    }
}

if (!function_exists('force_redirect')) {
    /**
     * @param string $url
     * @param int $code
     */
    function force_redirect($url, $code = 302)
    {
        try {
            app()->abort($code, '', [
                'Location' => $url
            ]);
        } catch (\Exception $e) {
            $handler = set_exception_handler(function () {});

            restore_error_handler();
            call_user_func($handler, $e);
            die;
        }
    }
}