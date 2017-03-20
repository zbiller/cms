<?php

namespace App\Traits;

use App\Options\CanCacheOptions;
use App\Exceptions\CacheException;

trait CanCache
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\CanCacheOptions file.
     *
     * @var CanCacheOptions
     */
    protected $canCacheOptions;

    /**
     * The method used for setting the refresh cache options.
     * This method should be called inside the model using this trait.
     * Inside the method, you should set all the refresh cache options.
     * This can be achieved using the methods from App\Options\CanCacheOptions.
     *
     * @return CanCacheOptions
     */
    abstract public function getCanCacheOptions(): CanCacheOptions;

    /**
     * On every database change, attempt to clear the cache.
     * This way, cache is kept in sync with the database table.
     *
     * @return void
     */
    public static function bootCanCache()
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
        $this->canCacheOptions = $this->getCanCacheOptions();

        $this->checkKey();

        cache()->forget($this->canCacheOptions->key);
    }

    /**
     * Verify if the Model using this trait has a $cache property defined.
     * The $cache property (array) should contain the "key" (the actual cache key name).
     *
     * @throws CacheException
     */
    private function checkKey()
    {
        if (!$this->canCacheOptions->key) {
            throw new CacheException(
                'Model ' . get_class($this) . ' uses the CanCache trait. ' . PHP_EOL .
                'You must set the key via the getCanCacheOptions() method from model.' . PHP_EOL .
                'Use the setKey() method from the App\Options\CanCacheOptions class.'
            );
        }
    }
}
