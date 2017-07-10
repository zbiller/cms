<?php

namespace App\Configs;

use App\Exceptions\ConfigException;

class CacheConfig
{
    /**
     * The config properties.
     *
     * @var mixed
     */
    public static $config;

    /**
     * The path of the upload config file.
     *
     * @var string
     */
    public static $path = 'config/cache.php';

    /**
     * Check if all the config options from config/cache.php are properly set.
     *
     * @throws ConfigException
     */
    public function __construct()
    {
        self::$config = config('cache');

        self::checkIfQueryCachingIsConfiguredProperly();
    }

    /**
     * Check if all the config options from config/cache.php are properly set.
     *
     * @return void
     */
    public static function check()
    {
        self::$config = config('cache');

        self::checkIfQueryCachingIsConfiguredProperly();
    }

    /**
     * Make all the necessary checks to see if everything under 'query' in config/cache.php is ok.
     * Check if query.prefix is defined.
     *
     * If something is wrong, throw a config exception.
     *
     * @return bool
     * @throws ConfigException
     */
    protected static function checkIfQueryCachingIsConfiguredProperly()
    {
        if (!isset(self::$config['query']['query_store']) || !self::$config['query']['query_store']) {
            throw new ConfigException(
                "The key 'query.query_store' does not exist or is empty in " .self::$path . "."
            );
        }

        if (!isset(self::$config['query']['duplicate_query_store']) || !self::$config['query']['duplicate_query_store']) {
            throw new ConfigException(
                "The key 'query.duplicate_query_store' does not exist or is empty in " .self::$path . "."
            );
        }

        if (!isset(self::$config['query']['query_prefix']) || !self::$config['query']['query_prefix']) {
            throw new ConfigException(
                "The key 'query.query_prefix' does not exist or is empty in " .self::$path . "."
            );
        }

        if (!isset(self::$config['query']['duplicate_query_prefix']) || !self::$config['query']['duplicate_query_prefix']) {
            throw new ConfigException(
                "The key 'query.duplicate_query_prefix' does not exist or is empty in " .self::$path . "."
            );
        }

        return true;
    }
}