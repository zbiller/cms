<?php

namespace App\Traits;

use App\Options\CacheOptions;
use App\Exceptions\CacheException;
use Exception;
use ReflectionMethod;

trait IsCacheable
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\CacheOptions file.
     *
     * @var CacheOptions
     */
    protected static $cacheOptions;

    /**
     * On every database change, attempt to clear the cache.
     * This way, cache is kept in sync with the database table.
     *
     * @return void
     */
    public static function bootIsCacheable()
    {
        self::checkCacheOptions();

        self::$cacheOptions = self::getCacheOptions();

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

        cache()->forget(self::$cacheOptions->key);
    }

    /**
     * Verify if the Model using this trait has a $cache property defined.
     * The $cache property (array) should contain the "key" (the actual cache key name).
     *
     * @throws CacheException
     */
    private function checkKey()
    {
        if (!self::$cacheOptions->key) {
            throw new CacheException(
                'Model ' . get_class($this) . ' uses the IsCacheable trait. ' . PHP_EOL .
                'You must set the key via the getCacheOptions() method from model.' . PHP_EOL .
                'Use the setKey() method from the App\Options\CacheOptions class.'
            );
        }
    }

    /**
     * Verify if the getCrudOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkCacheOptions()
    {
        if (!method_exists(self::class, 'getCacheOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getCacheOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getCacheOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getCacheOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}