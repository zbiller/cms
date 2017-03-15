<?php

namespace App\Traits;

use App\Options\CacheableOptions;
use App\Exceptions\CacheException;

trait Cacheable
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\CacheableOptions file.
     *
     * @var CacheableOptions
     */
    protected $cacheableOptions;

    /**
     * The method used for setting the refresh cache options.
     * This method should be called inside the model using this trait.
     * Inside the method, you should set all the refresh cache options.
     * This can be achieved using the methods from App\Options\CacheableOptions.
     *
     * @return CacheableOptions
     */
    abstract public function getCacheableOptions(): CacheableOptions;

    /**
     * On every database change, attempt to clear the cache.
     * This way, cache is kept in sync with the database table.
     *
     * @return void
     */
    public static function bootCacheable()
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
        $this->cacheableOptions = $this->getCacheableOptions();

        $this->checkKey();

        cache()->forget($this->cacheableOptions->key);
    }

    /**
     * Verify if the Model using this trait has a $cache property defined.
     * The $cache property (array) should contain the "key" (the actual cache key name).
     *
     * @throws CacheException
     */
    private function checkKey()
    {
        if (!$this->cacheableOptions->key) {
            throw new CacheException(
                'Model ' . get_class($this) . ' uses the Cacheable trait. ' . PHP_EOL .
                'You must set the key via the getCacheableOptions() method from model.' . PHP_EOL .
                'Use the setKey() method from the App\Options\CacheableOptions class.'
            );
        }
    }
}
