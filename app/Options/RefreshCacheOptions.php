<?php

namespace App\Options;

use App\Models\Model;

class RefreshCacheOptions
{
    /**
     * The cache key in use.
     *
     * @var
     */
    public $key;

    /**
     * Get a fresh instance of this class.
     *
     * @return RefreshCacheOptions
     */
    public static function instance(): RefreshCacheOptions
    {
        return new static();
    }

    /**
     * Set the key to work with in the App\Traits\RefreshesCache trait.
     *
     * @param string $key
     * @return RefreshCacheOptions
     */
    public function setKey($key): RefreshCacheOptions
    {
        $this->key = $key;

        return $this;
    }
}