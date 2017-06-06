<?php

namespace App\Traits;

trait HasAclCache
{
    /**
     * On every database change, attempt to clear the cache.
     * This way, cache is kept in sync with the database table.
     *
     * @return void
     */
    public static function bootIsCacheable()
    {
        static::created(function ($model) {
            $model->forgetAclCache();
        });

        static::updated(function ($model) {
            $model->forgetAclCache();
        });

        static::deleted(function ($model) {
            $model->forgetAclCache();
        });
    }

    /**
     * Checks for the cache key and after that delete the cache for that key.
     *
     * @return void
     */
    public function forgetAclCache()
    {
        cache()->forget('acl');
    }
}