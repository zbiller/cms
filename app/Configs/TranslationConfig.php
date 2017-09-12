<?php

namespace App\Configs;

use App\Exceptions\ConfigException;

class TranslationConfig
{
    /**
     * The config properties.
     *
     * @var mixed
     */
    public static $config;

    /**
     * The path of the translation config file.
     *
     * @var string
     */
    public static $path = 'config/translation.php';

    /**
     * Check if all the config options from config/translation.php are properly set.
     *
     * @return void
     */
    public static function check()
    {
        self::$config = config('translation');

        self::checkIfTranslationIsConfiguredProperly();
    }

    /**
     * Make all the necessary checks to see if everything in config/translation.php is ok.
     * If something is wrong, throw a config exception.
     *
     * @return bool
     * @throws ConfigException
     */
    protected static function checkIfTranslationIsConfiguredProperly()
    {
        if (!isset(self::$config['enable_translations'])) {
            throw new ConfigException(
                "The key 'enable_translations' does not exist in " .self::$path . "."
            );
        }

        if (!isset(self::$config['is_translatable_entity'])) {
            throw new ConfigException(
                "The key 'is_translatable_entity' does not exist in " .self::$path . "."
            );
        }

        return true;
    }
}