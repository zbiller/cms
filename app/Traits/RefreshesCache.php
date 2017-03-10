<?php

namespace App\Traits;

use App\Exceptions\CacheException;
use App\Options\RefreshCacheOptions;

trait RefreshesCache
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\RefreshCacheOptions file.
     *
     * @var RefreshCacheOptions
     */
    protected $refreshCacheOptions;

    /**
     * The method used for setting the refresh cache options.
     * This method should be called inside the model using this trait.
     * Inside the method, you should set all the refresh cache options.
     * This can be achieved using the methods from App\Options\RefreshCacheOptions.
     *
     * @return RefreshCacheOptions
     */
    abstract public function getRefreshCacheOptions(): RefreshCacheOptions;

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
        $this->refreshCacheOptions = $this->getRefreshCacheOptions();

        $this->checkKey();

        cache()->forget($this->refreshCacheOptions->key);
    }

    /**
     * Verify if the Model using this trait has a $cache property defined.
     * The $cache property (array) should contain the "key" (the actual cache key name).
     *
     * @throws CacheException
     */
    private function checkKey()
    {
        if (!$this->refreshCacheOptions->key) {
            throw new CacheException(
                'Model ' . get_class($this) . ' uses the RefreshesCache trait. ' . PHP_EOL .
                'You must set the key via the getRefreshCacheOptions() method from model.' . PHP_EOL .
                'Use the setKey() method from the App\Options\RefreshCacheOptions class.'
            );
        }
    }
}
