<?php

namespace App\Configs;

use App\Exceptions\ConfigException;

class ShopConfig
{
    /**
     * The config properties.
     *
     * @var mixed
     */
    public static $config;

    /**
     * The path of the shop config file.
     *
     * @var string
     */
    public static $path = 'config/shop.php';

    /**
     * Check if all the config options from config/shop.php are properly set.
     *
     * @return void
     */
    public static function check()
    {
        self::$config = config('shop');

        self::checkIfPriceIsConfiguredProperly();
        self::checkIfCartIsConfiguredProperly();
    }

    /**
     * Make all the necessary checks to see if everything in config/shop.php is ok.
     * If something is wrong, throw a config exception.
     *
     * @return bool
     * @throws ConfigException
     */
    protected static function checkIfPriceIsConfiguredProperly()
    {
        if (!isset(self::$config['price']['default_currency']) || !self::$config['price']['default_currency']) {
            throw new ConfigException(
                "The key 'price.default_currency' does not exist in " .self::$path . "."
            );
        }

        return true;
    }

    /**
     * Make all the necessary checks to see if everything in config/shop.php is ok.
     * If something is wrong, throw a config exception.
     *
     * @return bool
     * @throws ConfigException
     */
    protected static function checkIfCartIsConfiguredProperly()
    {
        if (!array_key_exists('delete_records_older_than', self::$config['cart'])) {
            throw new ConfigException(
                "The key 'cart.delete_records_older_than' does not exist in " . self::$path. "."
            );
        }

        return true;
    }
}