<?php

namespace App\Helpers;

use App\Models\Model;
use App\Models\Version\Draft;

class DraftHelper
{
    /**
     * Build the draft tab html.
     *
     * @param Model $model
     * @param string $route
     * @return \Illuminate\View\View
     */
    public function tab(Model $model, $route)
    {
        return view('helpers::draft.tab')->with([
            'model' => $model,
            'route' => $route,
        ]);
    }

    /**
     * Build the draft container html.
     *
     * @param Model $model
     * @return \Illuminate\View\View
     */
    public function container(Model $model)
    {
        return view('helpers::draft.container')->with([
            'model' => $model,
        ]);
    }

    /**
     * Build the additional draft view html.
     *
     * @param Draft $draft
     * @param Model $model
     * @return \Illuminate\View\View
     */
    public function view(Draft $draft, Model $model)
    {
        return view('helpers::draft.view')->with([
            'draft' => $draft,
            'model' => $model,
        ]);
    }
}