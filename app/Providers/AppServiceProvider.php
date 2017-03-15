<?php

namespace App\Providers;

use App\Helpers\Form\Admin;
use App\Helpers\Menu\Menu;
use App\Helpers\View\Pagination;
use App\Helpers\View\Validation;
use App\Helpers\Message\Flash;
use App\Helpers\View\Button;
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
        \Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFacades();
    }

    /**
     * Register the application's facades.
     *
     * @return void
     */
    protected function registerFacades()
    {
        $this->app->singleton('AdminForm', function ($app) {
            return new Admin($app);
        });

        $this->app->singleton('Menu', function ($app) {
            return new Menu($app);
        });

        $this->app->singleton('Pagination', function ($app) {
            return new Pagination($app);
        });

        $this->app->singleton('Validation', function ($app) {
            return new Validation($app);
        });

        $this->app->singleton('Flash', function ($app) {
            return new Flash($app);
        });

        $this->app->singleton('Button', function ($app) {
            return new Button($app);
        });

        $this->app->alias('adminform', Admin::class);
        $this->app->alias('menu', Menu::class);
        $this->app->alias('pagination', Pagination::class);
        $this->app->alias('validation', Validation::class);
        $this->app->alias('flash', Flash::class);
        $this->app->alias('button', Button::class);
    }
}
