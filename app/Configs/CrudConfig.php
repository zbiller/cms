<?php

namespace App\Configs;

use App\Exceptions\ConfigException;

class CrudConfig
{
    /**
     * The config properties.
     *
     * @var mixed
     */
    public static $config;

    /**
     * The path of the crud config file.
     *
     * @var string
     */
    public static $path = 'config/crud.php';

    /**
     * Check if all the config options from config/crud.php are properly set.
     *
     * @return void
     */
    public static function check()
    {
        self::$config = config('crud');

        self::checkIfCrudingIsConfiguredProperly();
    }

    /**
     * Make all the necessary checks to see if everything in config/crud.php is ok.
     * If something is wrong, throw a config exception.
     *
     * @return bool
     * @throws ConfigException
     */
    protected static function checkIfCrudingIsConfiguredProperly()
    {
        if (!array_key_exists('per_page', self::$config) || !is_numeric(self::$config['per_page'])) {
            throw new ConfigException(
                "The key 'per_page' does not exist in " .self::$path . " or is not an integer."
            );
        }

        if (!array_key_exists('soft_exceptions', self::$config) || !is_array(self::$config['soft_exceptions'])) {
            throw new ConfigException(
                "The key 'soft_exceptions' does not exist in " . self::$path. " or is not an array."
            );
        }

        return true;
    }
}