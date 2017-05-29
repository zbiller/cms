<?php

namespace App\Providers;

use App\Configs\UploadConfig;
use App\Configs\ActivityConfig;
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
            $this->checkUploadConfig();
            $this->checkActivityLogConfig();
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
     * Check if the config/upload.php is properly and fully configured.
     *
     * @return void
     */
    protected function checkUploadConfig()
    {
        UploadConfig::check();
    }

    /**
     * Check if the config/activity-log.php is properly and fully configured.
     *
     * @return void
     */
    protected function checkActivityLogConfig()
    {
        ActivityConfig::check();
    }
}
