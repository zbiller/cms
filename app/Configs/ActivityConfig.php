<?php

namespace App\Configs;

use App\Exceptions\ConfigException;

class ActivityConfig
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
    public static $path = 'config/activity-log.php';

    /**
     * Check if all the config options from config/activity-log.php are properly set.
     *
     * @throws ConfigException
     */
    public function __construct()
    {
        self::$config = config('activity');

        self::checkIfLoggingIsConfiguredProperly();
    }

    /**
     * Check if all the config options from config/cache.php are properly set.
     *
     * @return void
     */
    public static function check()
    {
        self::$config = config('activity');

        self::checkIfLoggingIsConfiguredProperly();
    }

    /**
     * Make all the necessary checks to see if everything under 'storage' in config/upload.php is ok.
     * Check if storage.disk is defined and if the specified disk is defined in config/filesystems.php also.
     *
     * If something is wrong, throw a config exception.
     *
     * @return bool
     * @throws ConfigException
     */
    protected static function checkIfLoggingIsConfiguredProperly()
    {
        if (!array_key_exists('enabled', self::$config)) {
            throw new ConfigException(
                "The key 'enabled' does not exist in " .self::$path . "."
            );
        }

        if (!array_key_exists('delete_records_older_than', self::$config)) {
            throw new ConfigException(
                "The key 'delete_records_older_than' does not exist in " . self::$path. "."
            );
        }

        return true;
    }
}