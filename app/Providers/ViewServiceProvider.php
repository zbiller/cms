<?php

namespace App\Providers;

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
        if (!app()->runningInConsole()) {
            view()->addNamespace('layouts', config('view.paths.layouts'));
            view()->addNamespace('helpers', config('view.paths.helpers'));
        }
    }
}