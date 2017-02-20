<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        if (!app()->runningInConsole()) {
            /* Define layouts view namespace */
            view()->addNamespace('layouts', config('view.paths.layouts'));
        }
    }
}