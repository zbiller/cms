<?php

namespace App\Helpers;

class JavascriptHelper
{
    /**
     * Render the actual view helper that displays flash messages.
     * Param $from: the name of the input of which it's value should be used for sluggifying.
     * Param $to: the name of the input in which the sluggified value should be inserted.
     *
     * @param string $from
     * @param string $to
     * @return \Illuminate\View\View
     */
    public function sluggify($from, $to)
    {
        return view("helpers::javascript.sluggify")->with([
            'from' => $from,
            'to' => $to
        ]);
    }
}