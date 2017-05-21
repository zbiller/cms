<?php

namespace App\Blocks\Clients;

use Illuminate\Contracts\View\View;

class Composer
{
    /**
     * The available locations for the block to be rendered in.
     *
	 * @param View $view
	 */
	public static $locations = [
        'content'
    ];

    /**
     * The block view logic.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $view->with('block', $view->model);
        $view->with('data', $view->model->metadata);
    }
}