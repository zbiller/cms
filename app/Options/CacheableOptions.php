<?php

namespace App\Options;

class CacheableOptions
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
     * @return CacheableOptions
     */
    public static function instance(): CacheableOptions
    {
        return new static();
    }

    /**
     * Set the key to work with in the App\Traits\Cacheable trait.
     *
     * @param string $key
     * @return CacheableOptions
     */
    public function setKey($key): CacheableOptions
    {
        $this->key = $key;

        return $this;
    }
}