<?php

namespace App\Helpers\View;

class Validation
{
    /**
     * Display the validation errors for a request.
     *
     * @return \Illuminate\View\View
     */
    public function errors()
    {
        return view('helpers::validation.errors');
    }
}