<?php

namespace App\Traits;

use Cache;
use App\Exceptions\CacheException;

trait RefreshesCache
{
    /**
     * The options necessary for the trait to work.
     * This property should be declared on the models using this trait.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * On every database change, attempt to clear the cache.
     * This way, cache is kept in sync with the database table.
     *
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
     * Checks for the cache key and after that delete the cache for that key.
     *
     * @throws CacheException
     */
    public function forgetCache()
    {
        $this->checkKey();

        cache()->forget($this->cache['key']);
    }

    /**
     * Verify if the Model using this trait has a $cache property defined.
     * The $cache property (array) should contain the "key" (the actual cache key name).
     *
     * @throws CacheException
     */
    private function checkKey()
    {
        if (!$this->cache || !isset($this->cache['key'])) {
            throw new CacheException(
                'Model ' . get_class($this) . ' uses the RefreshesCache trait. ' . PHP_EOL .
                'You have to define a "protected property $cache" (array) on that model. ' . PHP_EOL .
                'The property should contain at least: ["key" => "your cache key name"]'
            );
        }
    }
}
