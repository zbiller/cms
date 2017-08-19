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
     * The path of the activity config file.
     *
     * @var string
     */
    public static $path = 'config/activity.php';

    /**
     * Check if all the config options from config/activity.php are properly set.
     *
     * @return void
     */
    public static function check()
    {
        self::$config = config('activity');

        self::checkIfLoggingIsConfiguredProperly();
    }

    /**
     * Make all the necessary checks to see if everything in config/activity.php is ok.
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