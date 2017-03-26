<?php

namespace App\Providers;

use Schema;
use App\Helpers\FormAdminHelper;
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
        Schema::defaultStringLength(191);
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
        $this->app->singleton('FormAdmin', function ($app) {
            return new FormAdminHelper($app);
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

        $this->app->alias('form_admin', FormAdminHelper::class);
        $this->app->alias('menu', Menu::class);
        $this->app->alias('pagination', Pagination::class);
        $this->app->alias('validation', Validation::class);
        $this->app->alias('flash', Flash::class);
        $this->app->alias('button', Button::class);
    }
}
