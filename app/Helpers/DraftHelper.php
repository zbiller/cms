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
     * @param array $parameters
     * @return \Illuminate\View\View
     */
    public function tab(Model $model, $route, array $parameters = [])
    {
        return view('helpers::draft.tab')->with([
            'model' => $model,
            'route' => $route,
            'parameters' => $parameters,
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
     * @param string|null $approvalUrl
     * @return \Illuminate\View\View
     */
    public function view(Draft $draft, Model $model, $approvalUrl = null)
    {
        return view('helpers::draft.view')->with([
            'draft' => $draft,
            'model' => $model,
            'approvalUrl' => $approvalUrl,
        ]);
    }
}