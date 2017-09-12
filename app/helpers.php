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

if (!function_exists('uploader_lang')) {
    /**
     * @return \App\Helpers\UploaderLangHelper
     */
    function uploader_lang()
    {
        return app(UploaderLang::class);
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

if (!function_exists('form_admin_lang')) {
    /**
     * @return \App\Helpers\FormAdminHelper
     */
    function form_admin_lang()
    {
        return app(FormAdminLang::class);
    }
}

if (!function_exists('translation')) {
    /**
     * @return \App\Helpers\TranslationHelper
     */
    function translation()
    {
        return new App\Helpers\TranslationHelper();
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

if (!function_exists('draft')) {
    /**
     * @return \App\Helpers\DraftHelper
     */
    function draft()
    {
        return new App\Helpers\DraftHelper();
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

if (!function_exists('setting')) {
    /**
     * @return \App\Helpers\SettingHelper
     */
    function setting()
    {
        return new App\Helpers\SettingHelper();
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

if (!function_exists('preview')) {
    /**
     * @return \App\Helpers\PreviewHelper
     */
    function preview()
    {
        return new App\Helpers\PreviewHelper();
    }
}

if (!function_exists('activity_log')) {
    /**
     * @return \App\Helpers\ActivityHelper
     */
    function activity_log()
    {
        return new App\Helpers\ActivityHelper();
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

if (! function_exists('vd')) {
    /**
     * @param  mixed
     * @return void
     */
    function vd(...$args)
    {
        foreach ($args as $x) {
            (new Illuminate\Support\Debug\Dumper)->dump($x);
        }
    }
}

if (!function_exists('force_redirect')) {
    /**
     * @param string $url
     * @param int $code
     */
    function force_redirect($url, int $code = 302)
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

if (!function_exists('is_json_format')) {
    /**
     * @param $string
     * @return bool
     */
    function is_json_format($string)
    {
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('array_depth')) {
    /**
     * @param array $array
     * @return int
     */
    function array_depth(array $array) {
        $maxDepth = 1;

        foreach ($array as $value) {
            if (is_array($value)) {
                $depth = array_depth($value) + 1;

                if ($depth > $maxDepth) {
                    $maxDepth = $depth;
                }
            }
        }

        return $maxDepth;
    }
}

if (!function_exists('array_search_key_recursive')) {
    /**
     * @param string|int $needle
     * @param array $haystack
     * @param bool $regexp
     * @return mixed|null
     */
    function array_search_key_recursive($needle, array $haystack = [], $regexp = false)
    {
        $array = new \RecursiveIteratorIterator(
            new \RecursiveArrayIterator($haystack),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($array as $key => $value) {
            if ($regexp ? str_is($key, $needle) : $key === $needle) {
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