<?php

namespace App\Providers;

use Schema;
use App\Helpers\LibraryHelper;
use App\Helpers\MenuHelper;
use App\Helpers\PaginationHelper;
use App\Helpers\ValidationHelper;
use App\Helpers\ButtonHelper;
use App\Helpers\FlashHelper;
use App\Helpers\FormAdminHelper;
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
        $this->app->singleton('Library', function ($app) {
            return new LibraryHelper($app);
        });

        $this->app->singleton('Menu', function ($app) {
            return new MenuHelper($app);
        });

        $this->app->singleton('Pagination', function ($app) {
            return new PaginationHelper($app);
        });

        $this->app->singleton('Validation', function ($app) {
            return new ValidationHelper($app);
        });

        $this->app->singleton('Button', function ($app) {
            return new ButtonHelper($app);
        });

        $this->app->singleton('Flash', function ($app) {
            return new FlashHelper($app);
        });

        $this->app->singleton('FormAdmin', function ($app) {
            return new FormAdminHelper($app);
        });

        $this->app->alias('library', LibraryHelper::class);
        $this->app->alias('menu', MenuHelper::class);
        $this->app->alias('pagination', PaginationHelper::class);
        $this->app->alias('validation', ValidationHelper::class);
        $this->app->alias('button', ButtonHelper::class);
        $this->app->alias('flash', FlashHelper::class);
        $this->app->alias('form_admin', FormAdminHelper::class);
    }
}
