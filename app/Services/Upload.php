<?php

namespace App\Services;

use DB;
use Storage;
use Exception;
use Carbon\Carbon;
use App\Configs\Upload as UploadConfig;
use App\Exceptions\UploadException;
use Illuminate\Http\UploadedFile;

class Upload
{
    /**
     * The file instance coming from request()->file().
     *
     * @var UploadedFile
     */
    public $file;

    /**
     * The config options from config/upload.php
     *
     * @var array
     */
    public $config;

    /**
     * The filesystem disk used to store the uploaded files.
     *
     * @var string
     */
    protected $disk;

    /**
     * The name of the file to be uploaded with.
     *
     * @var string
     */
    protected $name;

    /**
     * The path of the file to be uploaded to.
     *
     * @var string
     */
    protected $path;

    /**
     * The client original file extension.
     *
     * @var string
     */
    protected $extension;

    /**
     * The client file size.
     *
     * @var string
     */
    protected $size;

    /**
     * The type of the file.
     * TYPE_IMAGE | TYPE_VIDEO | TYPE_AUDIO | TYPE_FILE
     *
     * @var int
     */
    protected $type;

    /**
     * The types a file can have.
     * This will be stored in the database -> uploads (table) -> type (column).
     *
     * @const
     */
    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;
    const TYPE_AUDIO = 3;
    const TYPE_FILE = 4;

    /**
     * All of the available image extensions.
     * These are used to determine if an uploaded file is actually an image.
     *
     * @var array
     */
    public static $images = [
        'jpeg',
        'jpg',
        'png',
        'gif',
        'bmp',
        'psd',
        'exif',
        'tiff',
        'ppm',
        'pgm',
        'pbm',
        'pnm',
        'webp',
        'heif',
        'bpg',
        'svg',
        'cgm',
    ];

    /**
     * All of the available video extensions.
     * These are used to determine if an uploaded file is actually a video.
     *
     * @var array
     */
    public static $videos = [
        'avi',
        'flv',
        'mp4',
        'ogg',
        'mov',
        'mpeg',
        'mpg',
        'mkv',
        'acc',
        'webm',
        'vob',
        'ogv',
        'drc',
        'gifv',
        'mng',
        'qt',
        'wmv',
        'yuv',
        'rm',
        'asv',
        'm4p',
        'm4v',
        'mp2',
        'mpe',
        'm2v',
        '3gp',
        '3g2',
        'mxf',
        'roq',
        'nsv',
        'f4v',
        'f4p',
        'f4a',
        'f4b',
    ];

    /**
     * All of the available audio extensions.
     * These are used to determine if an uploaded file is actually an audio.
     *
     * @var array
     */
    public static $audios = [
        'mp3',
        'aac',
        'wav',
        'ogg',
        '3gp',
        'aa',
        'aax',
        'act',
        'aiff',
        'amr',
        'ape',
        'au',
        'awb',
        'dct',
        'dss',
        'dvf',
        'flac',
        'gsm',
        'iklax',
        'ivs',
        'm4a',
        'mmf',
        'mpc',
        'msv',
        'oga',
        'opus',
        'ra',
        'rm',
        'raw',
        'sln',
        'tta',
        'vox',
        'wma',
        'wv',
    ];

    /**
     * Resolve dependencies automatically.
     * In order for this to happen, don't instantiate this normally using "new".
     * Use app(Upload::class) instead.
     *
     * @param UploadConfig $config
     */
    public function __construct(UploadConfig $config)
    {
        $this->config = $config->config;
    }

    /**
     * Set the file to work with.
     *
     * @param UploadedFile $file
     * @return $this
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get the file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the filesystem disk used for uploading files.
     * If no disk is specified in config/upload.php.
     * Then the "uploads" disk defined in config/filesystems.php will be used.
     *
     * @return $this
     */
    public function setDisk()
    {
        $this->disk = $this->config['storage']['disk'];

        return $this;
    }

    /**
     * Get the filesystem disk.
     *
     * @return string
     */
    public function getDisk()
    {
        return $this->disk;
    }

    /**
     * Set a unique name for the file.
     * This service works with UploadedFile instances
     * Because of this, the method "hasName" is always available.
     *
     * @return $this
     */
    public function setName()
    {
        $this->name = str_random(40) . '.' . $this->file->getClientOriginalExtension();

        if (Storage::disk($this->disk)->exists($this->path . '/' . $this->name)) {
            $this->setName();
        }

        return $this;
    }

    /**
     * Get the name of the file.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the path for the file.
     * The convention of the path is year/month/day (without leading zeros).
     *
     * @return $this
     */
    public function setPath()
    {
        $this->path = date('Y') . '/' . date('n') . '/' . date('j');

        return $this;
    }

    /**
     * Get the path of the file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the extension for the file.
     * The extension set is actually the client original extension.
     *
     * @return $this
     */
    public function setExtension()
    {
        $this->extension = $this->file->getClientOriginalExtension();

        return $this;
    }

    /**
     * Get the extension of the file.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set the extension for the file.
     * The extension set is actually the client original extension.
     *
     * @return $this
     */
    public function setSize()
    {
        $this->size = $this->file->getClientSize();

        return $this;
    }

    /**
     * Get the extension of the file.
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set the file type for storing in the database.
     * The file type can be one of the following constants defined in this class.
     * TYPE_IMAGE | TYPE_VIDEO | TYPE_AUDIO | TYPE_FILE
     *
     * @param int $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the type of the file.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Verify if the file is actually an image.
     *
     * @return bool
     */
    public function isImage()
    {
        return in_array(
            strtolower($this->file->getClientOriginalExtension()),
            array_map('strtolower', self::$images)
        );
    }

    /**
     * Verify if the file is actually a video.
     *
     * @return bool
     */
    public function isVideo()
    {
        return in_array(
            strtolower($this->file->getClientOriginalExtension()),
            array_map('strtolower', self::$videos)
        );
    }

    /**
     * Verify if the file is actually an audio.
     *
     * @return bool
     */
    public function isAudio()
    {
        return in_array(
            strtolower($this->file->getClientOriginalExtension()),
            array_map('strtolower', self::$audios)
        );
    }

    /**
     * Verify if the file is just a regular file.
     *
     * @return bool
     */
    public function isFile()
    {
        return !$this->isImage() && !$this->isVideo() && !$this->isAudio();
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws UploadException
     */
    public function upload(UploadedFile $file)
    {
        $this->setFile($file)->setDisk();
        $this->setPath()->setName()->setExtension()->setSize();

        try {
            switch ($this->file) {
                case $this->isImage():
                    //$this->storeImageToDisk();
                    break;
                case $this->isVideo():
                    //$this->storeVideoToDisk();
                    break;
                case $this->isAudio():
                    //$this->storeAudioToDisk();
                    break;
                case $this->isFile():
                    $this->storeFileToDisk();
                    break;
            }

            $this->saveFileToDatabase();

            return $this->name;
        } catch (UploadException $e) {
            $this->removeFromDisk();

            throw new UploadException($e->getMessage());
        }
    }

    /**
     * @return false|string
     * @throws UploadException
     */
    protected function storeFileToDisk()
    {
        $this->setType(self::TYPE_FILE);

        return $this->attemptStoringToDisk(function () {
            return $this->uploadSimple();
        });
    }

    /**
     * Simply upload (store) the given file.
     * When uploading, use the generated file name and file path.
     * The file will be stored on the disk provided in the config/upload.php file.
     *
     * @return false|string
     */
    protected function uploadSimple()
    {
        return $this->file->storePubliclyAs(
            $this->path, $this->name, $this->disk
        );
    }

    /**
     * @param callable $callback
     * @return mixed
     * @throws UploadException
     */
    protected function attemptStoringToDisk(callable $callback)
    {
        $upload = call_user_func($callback);

        if (!$upload) {
            throw new UploadException(
                'Failed uploading file(s)! Please try again.'
            );
        }

        return $upload;
    }

    /**
     * Remove a failed upload from disk.
     *
     * @return void
     */
    protected function removeFromDisk()
    {
        switch ($this->file) {
            case $this->isImage():
                //$this->removeImageFromDisk();
                break;
            case $this->isVideo():
                //$this->removeVideoFromDisk();
                break;
            case $this->isAudio():
                //$this->removeAudioFromDisk();
                break;
            case $this->isFile():
                $this->removeFileFromDisk();
                break;
        }
    }

    /**
     * Remove a previously stored file from disk.
     *
     * @return void
     */
    protected function removeFileFromDisk()
    {
        Storage::disk($this->disk)->delete($this->path . '/' . $this->name);
    }

    /**
     * Save details about the newly uploaded file into the database.
     * The details will be saved into the corresponding uploads database column.
     * The table where to save the file's details, can be set in config/upload.php -> database.table key.
     * Please note that the saving will be made only if the database.save key is set to true.
     *
     * @return bool
     * @throws UploadException
     */
    protected function saveFileToDatabase()
    {
        if ($this->config['database']['save'] !== true) {
            return true;
        }

        try {
            $result = DB::table($this->config['database']['table'])->insert([
                'namasae' => $this->getName(),
                'original_name' => $this->file->getClientOriginalName(),
                'path' => $this->getName(),
                'full_path' => $this->getPath() . '/' . $this->getName(),
                'extension' => $this->file->getExtension(),
                'size' => $this->getFile()->getClientSize(),
                'mime' => $this->file->getMimeType(),
                'type' => $this->getType(),
                'created_at' => Carbon::now()
            ]);


            if (!$result) {
                throw new Exception;
            }

            return true;
        } catch (Exception $e) {
            throw new UploadException(
                'Failed saving the uploaded file to the database! Please try again.'
            ) ;
        }
    }
}