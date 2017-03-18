<?php

namespace App\Configs;

use App\Exceptions\ConfigException;
use Schema;

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
     * @set config
     */
    public function __construct()
    {
        $this->config = config('upload');

        $this->checkIfStorageIsConfiguredProperly();
        $this->checkIfDatabaseIsConfiguredProperly();
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
}