<?php

namespace App\Options;

use Exception;

class CacheOptions
{
    /**
     * The cache key in use.
     *
     * @var
     */
    private $key;

    /**
     * Get the value of a property of this class.
     *
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (property_exists(static::class, $name)) {
            return $this->{$name};
        }

        throw new Exception(
            'The property "' . $name . '" does not exist in class "' . static::class . '"'
        );
    }

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