<?php

namespace App\Helpers\View;

class Validation
{
    /**
     * @return \Illuminate\View\View
     */
    public function errors()
    {
        return view('helpers::validation.errors');
    }
}