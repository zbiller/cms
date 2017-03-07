<?php

namespace App\Providers;

use Schema;
use App\Helpers\Menu\Menu;
use App\Helpers\View\Pagination;
use App\Helpers\View\Button;
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

        $this->app->singleton('Button', function ($app) {
            return new Button($app);
        });

        $this->app->alias('menu', Menu::class);
        $this->app->alias('pagination', Pagination::class);
        $this->app->alias('button', Button::class);
    }
}
