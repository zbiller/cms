<?php

namespace App\Providers;

use App\Helpers\Button;
use Schema;
use App\Helpers\Menu\Menu;
use App\Helpers\Pagination;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        $this->registerFacades();
    }

    /**
     * @return void
     */
    protected function registerFacades()
    {
        $this->app->singleton('Menu', function ($app) {
            return new Menu($app);
        });

        $this->app->singleton('Pagination', function ($app) {
            return new Pagination($app);
        });

        $this->app->alias('menu', Menu::class);
        $this->app->alias('pagination', Pagination::class);
    }
}
