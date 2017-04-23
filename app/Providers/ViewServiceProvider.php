<?php

namespace App\Providers;

use App\Models\Cms\Block;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /* setup layouts view path */
        view()->addNamespace('layouts', config('view.paths.layouts'));

        /* setup helpers view path */
        view()->addNamespace('helpers', config('view.paths.helpers'));

        /* setup blocks view paths */
        foreach (Block::$map as $type => $options) {
            view()->addNamespace("blocks_{$type}", realpath(base_path($options['views_path'])));
        }
    }
}