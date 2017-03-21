<?php

namespace App\Configs;

use Schema;
use App\Exceptions\ConfigException;

class UploadConfig
{
    /**
     * The config properties.
     *
     * @var mixed
     */
    public $config;

    /**
     * The path of the upload config file.
     *
     * @var string
     */
    public $path = 'config/upload.php';

    /**
     * @param array $config
     * @throws ConfigException
     */
    public function __construct(array $config = [])
    {
        $this->config = array_replace_recursive(config('upload'), $config);

        $this->checkIfStorageIsConfiguredProperly();
        $this->checkIfDatabaseIsConfiguredProperly();

        $this->checkIfImagesAreConfiguredProperly();
        $this->checkIfVideosAreConfiguredProperly();
        $this->checkIfAudiosAreConfiguredProperly();
        $this->checkIfFilesAreConfiguredProperly();
    }

    /**
     * Make all the necessary checks to see if everything under 'storage' in config/upload.php is ok.
     * Check if storage.disk is defined and if the specified disk is defined in config/filesystems.php also.
     * If something is wrong, throw a config exception.
     *
     * @throws ConfigException
     */
    protected function checkIfStorageIsConfiguredProperly()
    {
        if (!isset($this->config['storage']['disk']) || !$this->config['storage']['disk']) {
            throw new ConfigException(
                "The key 'storage.disk' does not exist in {$this->path}."
            );
        }

        if (!config('filesystems.disks.' . $this->config['storage']['disk'])) {
            throw new ConfigException(
                "The disk '{$this->config['storage']['disk']}' does not exist in config/filesystems.php"
            );
        }

        if (!isset($this->config['storage']['keep_old'])) {
            throw new ConfigException(
                "The key 'storage.keep_old' does not exist in {$this->path}."
            );
        }
    }

    /**
     * Make all the necessary checks to see if everything under 'database' in config/upload.php is ok.
     * Check if database.save and database.table are defined.
     * Check if the database.table defined actually exists in the database.
     *
     * If something is wrong, throw a config exception.
     *
     * @throws ConfigException
     */
    protected function checkIfDatabaseIsConfiguredProperly()
    {
        if (!isset($this->config['database']['save'])) {
            throw new ConfigException(
                "The key 'database.save' does not exist in {$this->path}."
            );
        }

        if ($this->config['database']['save'] === true) {
            if (!isset($this->config['database']['table']) || !$this->config['database']['table']) {
                throw new ConfigException(
                    "The key 'database.save' is true in {$this->path}." . PHP_EOL .
                    "You must also specify a 'database.table' key where to store the saved records."
                );
            }

            if (!Schema::hasTable($this->config['database']['table'])) {
                throw new ConfigException(
                    "The table defined in {$this->path} does not exist."
                );
            }
        }
    }

    /**
     * Make all the necessary checks to see if everything under 'images' in config/upload.php is ok.
     * Check if images.max_size, images.allowed_extensions images.styles exist.
     *
     * If something is wrong, throw a config exception.
     *
     * @throws ConfigException
     */
    protected function checkIfImagesAreConfiguredProperly()
    {
        if (!array_key_exists('max_size', $this->config['images'])) {
            throw new ConfigException(
                "The key 'images.max_size' does not exist in {$this->path}."
            );
        }

        if (!array_key_exists('allowed_extensions', $this->config['images'])) {
            throw new ConfigException(
                "The key 'images.allowed_extensions' does not exist in {$this->path}."
            );
        }

        if (!array_key_exists('quality', $this->config['images'])) {
            throw new ConfigException(
                "The key 'images.quality' does not exist in {$this->path}."
            );
        }

        if (!array_key_exists('styles', $this->config['images'])) {
            throw new ConfigException(
                "The key 'images.styles' does not exist in {$this->path}."
            );
        }

        foreach ($this->config['images']['styles'] as $field => $styles) {
            foreach ($styles as $name => $style) {
                if (!isset($style['width']) || !(int)$style['width'] || !isset($style['height']) || !(int)$style['height']) {
                    throw new ConfigException(
                        'Each image style must have at least the "width" and "height" properties defined.'
                    );
                }
            }
        }
    }

    /**
     * Make all the necessary checks to see if everything under 'videos' in config/upload.php is ok.
     * Check if audios.max_size and audios.allowed_extensions exist.
     *
     * If something is wrong, throw a config exception.
     *
     * @throws ConfigException
     */
    protected function checkIfVideosAreConfiguredProperly()
    {
        if (!array_key_exists('max_size', $this->config['videos'])) {
            throw new ConfigException(
                "The key 'videos.max_size' does not exist in {$this->path}."
            );
        }

        if (!array_key_exists('allowed_extensions', $this->config['videos'])) {
            throw new ConfigException(
                "The key 'videos.allowed_extensions' does not exist in {$this->path}."
            );
        }

        if (!array_key_exists('generate_thumbnails', $this->config['videos'])) {
            throw new ConfigException(
                "The key 'videos.generate_thumbnails' does not exist in {$this->path}."
            );
        }

        if (!array_key_exists('thumbnails_number', $this->config['videos'])) {
            throw new ConfigException(
                "The key 'videos.thumbnails_number' does not exist in {$this->path}."
            );
        }
    }

    /**
     * Make all the necessary checks to see if everything under 'audios' in config/upload.php is ok.
     * Check if audios.max_size and audios.allowed_extensions exist.
     *
     * If something is wrong, throw a config exception.
     *
     * @throws ConfigException
     */
    protected function checkIfAudiosAreConfiguredProperly()
    {
        if (!array_key_exists('max_size', $this->config['audios'])) {
            throw new ConfigException(
                "The key 'audio.max_size' does not exist in {$this->path}."
            );
        }

        if (!array_key_exists('allowed_extensions', $this->config['audios'])) {
            throw new ConfigException(
                "The key 'audio.allowed_extensions' does not exist in {$this->path}."
            );
        }
    }

    /**
     * Make all the necessary checks to see if everything under 'files' in config/upload.php is ok.
     * Check if files.max_size and files.allowed_extensions exist.
     *
     * If something is wrong, throw a config exception.
     *
     * @throws ConfigException
     */
    protected function checkIfFilesAreConfiguredProperly()
    {
        if (!array_key_exists('max_size', $this->config['files'])) {
            throw new ConfigException(
                "The key 'files.max_size' does not exist in {$this->path}."
            );
        }

        if (!array_key_exists('allowed_extensions', $this->config['files'])) {
            throw new ConfigException(
                "The key 'files.allowed_extensions' does not exist in {$this->path}."
            );
        }
    }
}