<?php

namespace App\Providers;

use App\Models\Cms\Block;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /* link admin menu composer */
        view()->composer('layouts::admin.partials._menu', 'App\Http\Composers\MenuComposer@admin');

        /* link admin language switcher composer */
        view()->composer('layouts::admin.partials._languages', 'App\Http\Composers\LanguagesComposer');

        /* link blocks composers */
        foreach (Block::$blocks as $type => $options) {
            view()->composer("blocks_{$type}::front", $options['composer_class']);
        }
    }
}