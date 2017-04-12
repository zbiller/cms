<?php

namespace App\Options;

class CacheOptions
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
     * @return CacheOptions
     */
    public static function instance(): CacheOptions
    {
        return new static();
    }

    /**
     * Set the key to work with in the App\Traits\IsCacheable trait.
     *
     * @param string $key
     * @return CacheOptions
     */
    public function setKey($key): CacheOptions
    {
        $this->key = $key;

        return $this;
    }
}