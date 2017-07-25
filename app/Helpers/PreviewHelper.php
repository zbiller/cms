<?php

namespace App\Helpers;

use DB;

class PreviewHelper
{
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