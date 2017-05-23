<?php

namespace App\Helpers;

use DB;

class PreviewHelper
{
    /**
     * Build a fully qualified URL for previewing.
     *
     * @param string $url
     * @return string
     */
    public function url($url)
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $query);

        $query = array_merge($query, ['preview' => true]);

        return strtok($url, '?') . '?' . http_build_query($query);
    }

    /**
     * If on preview, rollback the presumed changes so the record remains untouched.
     *
     * @return void
     */
    public function handle()
    {
        if (session()->has('is_preview') && session('is_preview') === true) {
            DB::rollBack();
        }
    }
}