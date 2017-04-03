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

if (!function_exists('library')) {
    /**
     * @return \App\Helpers\LibraryHelper
     */
    function library()
    {
        return app(Library::class);
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

if (!function_exists('menu')) {
    /**
     * @return \App\Helpers\MenuHelper
     */
    function menu()
    {
        return app(Menu::class);
    }
}

if (!function_exists('pagination')) {
    /**
     * @return \App\Helpers\PaginationHelper
     */
    function pagination()
    {
        return app(Pagination::class);
    }
}

if (!function_exists('validation')) {
    /**
     * @return \App\Helpers\ValidationHelper
     */
    function validation()
    {
        return app(Validation::class);
    }
}

if (!function_exists('button')) {
    /**
     * @return \App\Helpers\ButtonHelper
     */
    function button()
    {
        return app(Button::class);
    }
}

if (!function_exists('flash')) {
    /**
     * @return \App\Helpers\FlashHelper
     */
    function flash()
    {
        return app(Flash::class);
    }
}

if (!function_exists('form_admin')) {
    /**
     * @return \App\Helpers\FormAdminHelper
     */
    function form_admin()
    {
        return app(FormAdmin::class);
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

if (!function_exists('array_search_key_recursive')) {
    /**
     * @param string|int $needle
     * @param array $haystack
     * @return mixed|null
     */
    function array_search_key_recursive($needle, array $haystack = [])
    {
        $array = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($haystack),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($array as $key => $value) {
            if ($key === $needle) {
                return $value;
            }
        }

        return null;
    }
}