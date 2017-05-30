<?php

namespace App\Providers;

use Schema;
use App\Helpers\UploaderHelper;
use App\Helpers\FormAdminHelper;
use App\Helpers\MetaHelper;
use App\Helpers\RelationHelper;
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
        $this->app->singleton('Uploader', function ($app) {
            return new UploaderHelper($app);
        });

        $this->app->singleton('FormAdmin', function ($app) {
            return new FormAdminHelper($app);
        });

        $this->app->singleton('Meta', function ($app) {
            return new MetaHelper($app);
        });

        $this->app->singleton('Relation', function ($app) {
            return new RelationHelper($app);
        });

        $this->app->alias('uploader', UploaderHelper::class);
        $this->app->alias('form_admin', FormAdminHelper::class);
        $this->app->alias('meta', MetaHelper::class);
        $this->app->alias('relation', RelationHelper::class);
    }
}
