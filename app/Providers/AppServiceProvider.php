<?php

namespace App\Providers;

use App\Helpers\FormAdminHelper;
use App\Helpers\FormAdminLangHelper;
use App\Helpers\MetaHelper;
use App\Helpers\UploaderHelper;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
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

        if ($this->app->environment() != 'development') {
            DB::connection()->disableQueryLog();
        }

        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
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
        $this->app->singleton('Meta', function ($app) {
            return new MetaHelper($app);
        });

        $this->app->singleton('Uploader', function ($app) {
            return new UploaderHelper($app);
        });

        $this->app->singleton('FormAdmin', function ($app) {
            return new FormAdminHelper($app);
        });

        $this->app->singleton('FormAdminLang', function ($app) {
            return new FormAdminLangHelper($app);
        });

        $this->app->alias('meta', MetaHelper::class);
        $this->app->alias('uploader', UploaderHelper::class);
        $this->app->alias('form_admin', FormAdminHelper::class);
        $this->app->alias('form_admin_lang', FormAdminLangHelper::class);
    }
}
