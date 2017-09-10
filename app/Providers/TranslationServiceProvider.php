<?php

namespace App\Providers;

use Blade;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerBladeIfs();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register blade directives for language.
     *
     * @return void
     */
    protected function registerBladeIfs()
    {
        Blade::{'if'}('istranslatable', function () {
            return config('language.is_translatable') === true;
        });
    }
}
