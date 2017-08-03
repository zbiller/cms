<?php

namespace App\Providers;

use App\Services\CartService;
use DB;
use Illuminate\Session\SessionManager;
use Schema;
use App\Helpers\UploaderHelper;
use App\Helpers\FormAdminHelper;
use App\Helpers\MetaHelper;
use App\Helpers\RelationHelper;
use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('cart', new CartService(app('session')));
    }
}
