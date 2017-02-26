<?php

namespace App\Providers;

use Schema;
use App\Helpers\Menu\Menu;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Menu', function ($app) {
            return new Menu($app);
        });

        $this->app->alias('menu', Menu::class);
    }
}
