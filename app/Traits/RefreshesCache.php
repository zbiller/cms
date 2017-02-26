<?php

namespace App\Traits;

use Cache;
use App\Exceptions\CacheException;

trait RefreshesCache
{
    /**
     * @return void
     */
    public static function bootRefreshesCache()
    {
        static::created(function ($model) {
            $model->forgetCache();
        });

        static::updated(function ($model) {
            $model->forgetCache();
        });

        static::deleted(function ($model) {
            $model->forgetCache();
        });
    }

    /**
     * @throws CacheException
     */
    public function forgetCache()
    {
        if (!$this->cache || !isset($this->cache['key'])) {
            throw new CacheException(
                'Model ' . get_class($this) . ' uses the RefreshesCache trait. ' . PHP_EOL .
                'You have to define a "protected property $cache" (array) on that model. ' . PHP_EOL .
                'The property should contain: ["key" => "your cache key name"]'
            );
        }

        Cache::forget($this->cache['key']);
    }
}
