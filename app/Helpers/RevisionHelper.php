<?php

namespace App\Helpers;

use App\Models\Model;
use App\Models\Version\Revision;

class RevisionHelper
{
    /**
     * Build the revision tab html.
     *
     * @param Model $model
     * @param string $route
     * @return \Illuminate\View\View
     */
    public function tab(Model $model, $route)
    {
        return view('helpers::revision.tab')->with([
            'model' => $model,
            'route' => $route,
        ]);
    }

    /**
     * Build the revision container html.
     *
     * @param Model $model
     * @return \Illuminate\View\View
     */
    public function container(Model $model)
    {
        return view('helpers::revision.container')->with([
            'model' => $model,
        ]);
    }

    /**
     * Build the additional revision view html.
     *
     * @param Revision $revision
     * @param Model $model
     * @return \Illuminate\View\View
     */
    public function view(Revision $revision, Model $model)
    {
        return view('helpers::revision.view')->with([
            'revision' => $revision,
            'model' => $model,
        ]);
    }
}