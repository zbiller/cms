<?php

if (!function_exists('uploaded')) {
    /**
     * @param string $file
     * @return \App\Helpers\UploadedHelper
     */
    function uploaded($file)
    {
        return new \App\Helpers\UploadedHelper($file);
    }
}

if (!function_exists('uploader')) {
    /**
     * @return \App\Helpers\UploaderHelper
     */
    function uploader()
    {
        return app(Uploader::class);
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

if (!function_exists('form_admin')) {
    /**
     * @return \App\Helpers\FormAdminHelper
     */
    function form_admin()
    {
        return app(FormAdmin::class);
    }
}

if (!function_exists('flash')) {
    /**
     * @param string|null $type
     * @return \App\Helpers\FlashHelper
     */
    function flash($type = null)
    {
        return new App\Helpers\FlashHelper($type);
    }
}

if (!function_exists('menu')) {
    /**
     * @return \App\Helpers\MenuHelper
     */
    function menu()
    {
        return new \App\Helpers\MenuHelper();
    }
}

if (!function_exists('pagination')) {
    /**
     * @param string|null $type
     * @return \App\Helpers\PaginationHelper
     */
    function pagination($type = null)
    {
        return new App\Helpers\PaginationHelper($type);
    }
}

if (!function_exists('validation')) {
    /**
     * @param string|null $type
     * @return \App\Helpers\ValidationHelper
     */
    function validation($type = null)
    {
        return new App\Helpers\ValidationHelper($type);
    }
}

if (!function_exists('button')) {
    /**
     * @return \App\Helpers\ButtonHelper
     */
    function button()
    {
        return new App\Helpers\ButtonHelper();
    }
}

if (!function_exists('page')) {
    /**
     * @return \App\Helpers\PageHelper
     */
    function page()
    {
        return new App\Helpers\PageHelper();
    }
}

if (!function_exists('block')) {
    /**
     * @return \App\Helpers\BlockHelper
     */
    function block()
    {
        return new App\Helpers\BlockHelper();
    }
}

if (!function_exists('revision')) {
    /**
     * @return \App\Helpers\RevisionHelper
     */
    function revision()
    {
        return new App\Helpers\RevisionHelper();
    }
}

if (!function_exists('js')) {
    /**
     * @return \App\Helpers\JavascriptHelper
     */
    function js()
    {
        return new App\Helpers\JavascriptHelper();
    }
}

if (!function_exists('relation')) {
    /**
     * @return \App\Helpers\RelationHelper
     */
    function relation()
    {
        return new App\Helpers\RelationHelper();
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
     * @param bool $match
     * @return mixed|null
     */
    function array_search_key_recursive($needle, array $haystack = [], $match = false)
    {
        $array = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($haystack),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($array as $key => $value) {
            if ($match ? str_is($key, $needle) : $key === $needle) {
                return $value;
            }
        }

        return null;
    }
}

if (!function_exists('get_object_vars_recursive')) {

    function get_object_vars_recursive($object)
    {
        $result = [];
        $vars = is_object($object) ? get_object_vars($object) : $object;

        foreach ($vars as $key => $value) {
            $value = (is_array($value) || is_object($value)) ? get_object_vars_recursive($value) : $value;
            $result[$key] = $value;
        }

        return $result;
    }
}