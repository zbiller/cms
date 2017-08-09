<?php

namespace App\Providers;

use App\Helpers\FormAdminHelper;
use App\Helpers\MetaHelper;
use App\Helpers\RelationHelper;
use App\Helpers\UploaderHelper;
use DB;
use Illuminate\Support\ServiceProvider;
use Schema;

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

        if (env('APP_ENV', 'development') != 'development') {
            DB::connection()->disableQueryLog();
        }
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

        $this->app->alias('uploader', UploaderHelper::class);
        $this->app->alias('form_admin', FormAdminHelper::class);
        $this->app->alias('meta', MetaHelper::class);
    }
}
