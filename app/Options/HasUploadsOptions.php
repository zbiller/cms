<?php

namespace App\Options;

class HasUploadsOptions
{
    /**
     * The storage disk where to store uploaded files.
     *
     * @var string
     */
    public $storageDisk;

    /**
     * @var bool
     */
    public $databaseSave;

    /**
     * @var string
     */
    public $databaseTable;

    /**
     * @var int
     */
    public $imageMaxSize;

    /**
     * @var array|string
     */
    public $imageAllowedExtensions;

    /**
     * @var array
     */
    public $imageStyles;

    /**
     * @var int
     */
    public $videoMaxSize;

    /**
     * @var array|string
     */
    public $videoAllowedExtensions;

    /**
     * @var bool
     */
    public $videoGenerateThumbnails;

    /**
     * @var int
     */
    public $videoThumbnailsNumber;

    /**
     * @var int
     */
    public $audioMaxSize;

    /**
     * @var array|string
     */
    public $audioAllowedExtensions;

    /**
     * @var int
     */
    public $fileMaxSize;

    /**
     * @var array|string
     */
    public $fileAllowedExtensions;

    /**
     * Get a fresh instance of this class.
     *
     * @return HasUploadsOptions
     */
    public static function instance(): HasUploadsOptions
    {
        return new static();
    }

    /**
     * Set the storage disk to work with in:
     * App\Traits\HasUploads, App\Config\Upload.
     *
     * @param string $disk
     * @return HasUploadsOptions
     */
    public function setStorageDisk($disk): HasUploadsOptions
    {
        $this->storageDisk = $disk;

        return $this;
    }

    /**
     * Set the database save flag to work with in:
     * App\Traits\HasUploads, App\Config\Upload.
     *
     * @param boolean $save
     * @return HasUploadsOptions
     */
    public function setDatabaseSave($save): HasUploadsOptions
    {
        $this->databaseSave = (bool)$save;

        return $this;
    }
}