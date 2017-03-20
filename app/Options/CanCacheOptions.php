<?php

namespace App\Options;

class CanCacheOptions
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
     * @return CanCacheOptions
     */
    public static function instance(): CanCacheOptions
    {
        return new static();
    }

    /**
     * Set the key to work with in the App\Traits\CanCache trait.
     *
     * @param string $key
     * @return CanCacheOptions
     */
    public function setKey($key): CanCacheOptions
    {
        $this->key = $key;

        return $this;
    }
}