<?php

namespace App\Configs;

use Schema;
use App\Exceptions\ConfigException;

class Upload
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
                'The property "storage.disk" does not exist in ' . $this->path . '.'
            );
        }

        if (!config('filesystems.disks.' . $this->config['storage']['disk'])) {
            throw new ConfigException(
                'The disk "' . $this->config['storage']['disk'] . '" does not exist in config/filesystems.php'
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
                'The property "database.save" does not exist in ' . $this->path . '.'
            );
        }

        if ($this->config['database']['save'] === true) {
            if (!isset($this->config['database']['table']) || !$this->config['database']['table']) {
                throw new ConfigException(
                    'The property "database.save" is true in ' . $this->path . '.' . PHP_EOL .
                    'You must also specify a "database.table" where to store the saved records.'
                );
            }

            if (!Schema::hasTable($this->config['database']['table'])) {
                throw new ConfigException(
                    'The table defined in ' . $this->path . ' does not exist.'
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
                'The images.max_size key from ' . $this->path . ' is not specified.'
            );
        }

        if (!array_key_exists('allowed_extensions', $this->config['images'])) {
            throw new ConfigException(
                'The images.allowed_extensions key from ' . $this->path . ' is not specified.'
            );
        }

        if (!array_key_exists('quality', $this->config['images'])) {
            throw new ConfigException(
                'The images.quality key from ' . $this->path . ' is not specified.'
            );
        }

        if (!array_key_exists('styles', $this->config['images'])) {
            throw new ConfigException(
                'The images.styles key from ' . $this->path . ' is not specified.'
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
                'The videos.max_size key from ' . $this->path . ' is not specified.'
            );
        }

        if (!array_key_exists('allowed_extensions', $this->config['videos'])) {
            throw new ConfigException(
                'The videos.allowed_extensions key from ' . $this->path . ' is not specified.'
            );
        }

        if (!array_key_exists('generate_thumbnails', $this->config['videos'])) {
            throw new ConfigException(
                'The videos.generate_thumbnails key from ' . $this->path . ' is not specified.'
            );
        }

        if (!array_key_exists('thumbnails_number', $this->config['videos'])) {
            throw new ConfigException(
                'The videos.thumbnails_number key from ' . $this->path . ' is not specified.'
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
                'The audios.max_size key from ' . $this->path . ' is not specified.'
            );
        }

        if (!array_key_exists('allowed_extensions', $this->config['audios'])) {
            throw new ConfigException(
                'The audios.allowed_extensions key from ' . $this->path . ' is not specified.'
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
                'The files.max_size key from ' . $this->path . ' is not specified.'
            );
        }

        if (!array_key_exists('allowed_extensions', $this->config['files'])) {
            throw new ConfigException(
                'The files.allowed_extensions key from ' . $this->path . ' is not specified.'
            );
        }
    }
}