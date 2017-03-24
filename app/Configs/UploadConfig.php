<?php

namespace App\Configs;

use App\Exceptions\ConfigException;

class UploadConfig
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
    public static $path = 'config/upload.php';

    /**
     * Merge the config using:
     * Contents of config/upload.php and the array provided in the getUploadConfig() method defined on the model class.
     * Check if all the config options from config/upload.php are properly set.
     *
     * @param array $config
     * @throws ConfigException
     */
    public function __construct(array $config = [])
    {
        self::$config = array_replace_recursive(config('upload'), $config);

        $this->checkIfStorageIsConfiguredProperly();
        $this->checkIfDatabaseIsConfiguredProperly();

        $this->checkIfImagesAreConfiguredProperly();
        $this->checkIfVideosAreConfiguredProperly();
        $this->checkIfAudiosAreConfiguredProperly();
        $this->checkIfFilesAreConfiguredProperly();
    }

    /**
     * Check if all the config options from config/upload.php are properly set.
     *
     * @return void
     */
    public static function check()
    {
        self::$config = config('upload');

        self::checkIfStorageIsConfiguredProperly();
        self::checkIfDatabaseIsConfiguredProperly();
        self::checkIfImagesAreConfiguredProperly();
        self::checkIfVideosAreConfiguredProperly();
        self::checkIfAudiosAreConfiguredProperly();
        self::checkIfFilesAreConfiguredProperly();
    }

    /**
     * Make all the necessary checks to see if everything under 'storage' in config/upload.php is ok.
     * Check if storage.disk is defined and if the specified disk is defined in config/filesystems.php also.
     *
     * If something is wrong, throw a config exception.
     *
     * @return $this
     * @throws ConfigException
     */
    protected static function checkIfStorageIsConfiguredProperly()
    {
        if (!isset(self::$config['storage']['disk']) || !self::$config['storage']['disk']) {
            throw new ConfigException(
                "The key 'storage.disk' does not exist in " .self::$path . "."
            );
        }

        if (!config('filesystems.disks.' . self::$config['storage']['disk'])) {
            throw new ConfigException(
                "The disk '" . self::$config['storage']['disk'] . "' does not exist in config/filesystems.php"
            );
        }

        if (!isset(self::$config['storage']['keep_old'])) {
            throw new ConfigException(
                "The key 'storage.keep_old' does not exist in " . self::$path. "."
            );
        }

        return true;
    }

    /**
     * Make all the necessary checks to see if everything under 'database' in config/upload.php is ok.
     * Check if database.save and database.table are defined.
     * Check if the database.table defined actually exists in the database.
     *
     * If something is wrong, throw a config exception.
     *
     * @return $this
     * @throws ConfigException
     */
    protected static function checkIfDatabaseIsConfiguredProperly()
    {
        if (!isset(self::$config['database']['save'])) {
            throw new ConfigException(
                "The key 'database.save' does not exist in " . self::$path . "."
            );
        }

        if (self::$config['database']['save'] === true) {
            if (!isset(self::$config['database']['table']) || !self::$config['database']['table']) {
                throw new ConfigException(
                    "The key 'database.save' is true in " . self::$path . "." . PHP_EOL .
                    "You must also specify a 'database.table' key where to store the saved records."
                );
            }
        }

        return true;
    }

    /**
     * Make all the necessary checks to see if everything under 'images' in config/upload.php is ok.
     * Check if images.max_size, images.allowed_extensions images.styles exist.
     *
     * If something is wrong, throw a config exception.
     *
     * @return $this
     * @throws ConfigException
     */
    protected static function checkIfImagesAreConfiguredProperly()
    {
        if (!array_key_exists('max_size', self::$config['images'])) {
            throw new ConfigException(
                "The key 'images.max_size' does not exist in " . self::$path . "."
            );
        }

        if (!array_key_exists('allowed_extensions', self::$config['images'])) {
            throw new ConfigException(
                "The key 'images.allowed_extensions' does not exist in " . self::$path . "."
            );
        }

        if (!array_key_exists('quality', self::$config['images'])) {
            throw new ConfigException(
                "The key 'images.quality' does not exist in " . self::$path . "."
            );
        }

        if (!array_key_exists('styles', self::$config['images'])) {
            throw new ConfigException(
                "The key 'images.styles' does not exist in " . self::$path . "."
            );
        }

        foreach (self::$config['images']['styles'] as $field => $styles) {
            foreach ($styles as $name => $style) {
                if (!isset($style['width']) || !(int)$style['width'] || !isset($style['height']) || !(int)$style['height']) {
                    throw new ConfigException(
                        'Each image style must have at least the "width" and "height" properties defined.'
                    );
                }
            }
        }

        return true;
    }

    /**
     * Make all the necessary checks to see if everything under 'videos' in config/upload.php is ok.
     * Check if audios.max_size and audios.allowed_extensions exist.
     *
     * If something is wrong, throw a config exception.
     *
     * @return $this
     * @throws ConfigException
     */
    protected static function checkIfVideosAreConfiguredProperly()
    {
        if (!array_key_exists('max_size', self::$config['videos'])) {
            throw new ConfigException(
                "The key 'videos.max_size' does not exist in " . self::$path . "."
            );
        }

        if (!array_key_exists('allowed_extensions', self::$config['videos'])) {
            throw new ConfigException(
                "The key 'videos.allowed_extensions' does not exist in " . self::$path . "."
            );
        }

        if (!array_key_exists('generate_thumbnails', self::$config['videos'])) {
            throw new ConfigException(
                "The key 'videos.generate_thumbnails' does not exist in " . self::$path . "."
            );
        }

        if (!array_key_exists('thumbnails_number', self::$config['videos'])) {
            throw new ConfigException(
                "The key 'videos.thumbnails_number' does not exist in " . self::$path . "."
            );
        }

        return true;
    }

    /**
     * Make all the necessary checks to see if everything under 'audios' in config/upload.php is ok.
     * Check if audios.max_size and audios.allowed_extensions exist.
     *
     * If something is wrong, throw a config exception.
     *
     * @return $this
     * @throws ConfigException
     */
    protected static function checkIfAudiosAreConfiguredProperly()
    {
        if (!array_key_exists('max_size', self::$config['audios'])) {
            throw new ConfigException(
                "The key 'audio.max_size' does not exist in " . self::$path . "."
            );
        }

        if (!array_key_exists('allowed_extensions', self::$config['audios'])) {
            throw new ConfigException(
                "The key 'audio.allowed_extensions' does not exist in " . self::$path . "."
            );
        }

        return true;
    }

    /**
     * Make all the necessary checks to see if everything under 'files' in config/upload.php is ok.
     * Check if files.max_size and files.allowed_extensions exist.
     *
     * If something is wrong, throw a config exception.
     *
     * @return $this
     * @throws ConfigException
     */
    protected static function checkIfFilesAreConfiguredProperly()
    {
        if (!array_key_exists('max_size', self::$config['files'])) {
            throw new ConfigException(
                "The key 'files.max_size' does not exist in " . self::$path . "."
            );
        }

        if (!array_key_exists('allowed_extensions', self::$config['files'])) {
            throw new ConfigException(
                "The key 'files.allowed_extensions' does not exist in " . self::$path . "."
            );
        }

        return true;
    }
}