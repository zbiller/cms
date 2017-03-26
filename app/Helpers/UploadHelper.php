<?php

namespace App\Helpers;

use Storage;
use App\Models\Upload\Upload;
use App\Services\UploadService;

class UploadHelper
{
    /**
     * The full path to the file.
     *
     * @var string
     */
    protected $file;

    /**
     * The filesystem disk used to search the files in.
     *
     * @var string
     */
    protected $disk;

    /**
     * The extension of the provided file.
     *
     * @var string
     */
    protected $extension;

    /**
     * The type of the file.
     * TYPE_NORMAL | TYPE_IMAGE | TYPE_VIDEO
     *
     * @var string
     */
    protected $type;

    /**
     * The types a file can have.
     * This will be used by this helper to resolve methods specifically by file type.
     *
     * @const
     */
    const TYPE_NORMAL = 1;
    const TYPE_IMAGE = 2;
    const TYPE_VIDEO = 3;

    /**
     * Build a fully configured UploadHelper instance.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->setFile($file)->setDisk()->setExtension()->setType();
    }

    /**
     * Set the file to work with.
     *
     * @param string $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Set the storage disk to work with.
     *
     * @return $this
     */
    public function setDisk()
    {
        $this->disk = config('upload.storage.disk');

        return $this;
    }

    /**
     * Set the storage disk to work with.
     *
     * @return $this
     */
    public function setExtension()
    {
        $this->extension = strtolower(last(explode('.', $this->file)));

        return $this;
    }

    /**
     * Set the file type.
     *
     * @return $this
     */
    public function setType()
    {
        switch ($this->extension) {
            case in_array($this->extension, UploadService::$images):
                $this->type = self::TYPE_IMAGE;
                break;
            case in_array($this->extension, UploadService::$videos):
                $this->type = self::TYPE_VIDEO;
                break;
            default:
                $this->type = self::TYPE_NORMAL;
                break;
        }

        return $this;
    }

    /**
     * Set the $file to the exact path of the provided video's thumbnail.
     * The $number parameter is used to specify which video thumbnail to identify: 1st, 2nd, 3rd, etc.
     * Keep in mind that this method will only have an effect on video type files.
     *
     * @param int|null $number
     * @return $this
     */
    public function thumbnail($number = null)
    {
        if ($this->type == self::TYPE_IMAGE) {
            $this->file = substr_replace(
                preg_replace('/\..+$/', '.' . $this->extension, $this->file), '_thumbnail', strpos($this->file, '.'), 0
            );
        }

        if ($this->type == self::TYPE_VIDEO) {
            $this->file = substr_replace(
                preg_replace('/\..+$/', '.jpg', $this->file), '_thumbnail_' . $number, strpos($this->file, '.'), 0
            );
        }

        return $this->url();
    }

    /**
     * Get the parsed file's full url.
     * You can specify which style instance of the file you want to get.
     * However, specifying the style is taking into consideration only if the file is an actual image.
     *
     * @param string|null $style
     * @return string
     */
    public function url($style = null)
    {
        if ($style && ($this->type == self::TYPE_IMAGE || $this->type == self::TYPE_VIDEO)) {
            $this->file = substr_replace(
                $this->file, '_' . $style, strpos($this->file, '.'), 0
            );
        }

        return Storage::disk($this->disk)->url($this->file);
    }

    /**
     * Get the Upload instance representing the field's upload.
     *
     * @return Upload|null
     */
    public function load()
    {
        return config('upload.database.save') ? Upload::whereFullPath($this->file)->first() : null;
    }

    /**
     * Check if the given file exists in storage.
     *
     * @return bool
     */
    public function exists()
    {
        return Storage::disk($this->disk)->exists($this->file);
    }
}