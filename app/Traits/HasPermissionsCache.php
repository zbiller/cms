<?php

namespace App\Traits;

trait HasPermissionsCache
{
    /**
     * On every database change, attempt to clear the cache.
     * This way, cache is kept in sync with the database table.
     *
     * @return void
     */
    public static function HasAclCache()
    {
        static::created(function ($model) {
            $model->forgetPermissionsCache();
        });

        static::updated(function ($model) {
            $model->forgetPermissionsCache();
        });

        static::deleted(function ($model) {
            $model->forgetPermissionsCache();
        });
    }

    /**
     * Checks for the cache key and after that delete the cache for that key.
     *
     * @return void
     */
    public function forgetPermissionsCache()
    {
        cache()->forget('permissions');
    }
}