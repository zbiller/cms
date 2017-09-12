<?php

namespace App\Providers;

use App\Configs\ActivityConfig;
use App\Configs\CacheConfig;
use App\Configs\CrudConfig;
use App\Configs\ShopConfig;
use App\Configs\TranslationConfig;
use App\Configs\UploadConfig;
use Illuminate\Support\ServiceProvider;

class ConfigServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!app()->runningInConsole()) {
            $this->checkCrudConfig();
            $this->checkUploadConfig();
            $this->checkCacheConfig();
            $this->checkTranslationConfig();
            $this->checkShopConfig();
            $this->checkActivityConfig();
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Check if the config/crud.php is properly and fully configured.
     *
     * @return void
     */
    protected function checkCrudConfig()
    {
        CrudConfig::check();
    }

    /**
     * Check if the config/upload.php is properly and fully configured.
     *
     * @return void
     */
    protected function checkUploadConfig()
    {
        UploadConfig::check();
    }

    /**
     * Check if the config/cache.php is properly and fully configured.
     *
     * @return void
     */
    protected function checkCacheConfig()
    {
        CacheConfig::check();
    }

    /**
     * Check if the config/translation.php is properly and fully configured.
     *
     * @return void
     */
    protected function checkTranslationConfig()
    {
        TranslationConfig::check();
    }

    /**
     * Check if the config/shop.php is properly and fully configured.
     *
     * @return void
     */
    protected function checkShopConfig()
    {
        ShopConfig::check();
    }

    /**
     * Check if the config/activity.php is properly and fully configured.
     *
     * @return void
     */
    protected function checkActivityConfig()
    {
        ActivityConfig::check();
    }
}
