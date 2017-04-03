<?php

namespace App\Services;

use DB;
use Storage;
use Validator;
use Image;
use Closure;
use Exception;
use FFMpeg;
use App\Models\Model;
use App\Models\Upload\Upload;
use App\Http\Requests\UploadRequest;
use App\Exceptions\UploadException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;

class UploadService
{
    /**
     * The original record from the database.
     *
     * @var Upload
     */
    protected $original;

    /**
     * The file instance coming from request()->file().
     *
     * @var UploadedFile
     */
    protected $file;

    /**
     * The corresponding model class for the upload.
     *
     * @var Model
     */
    protected $model;

    /**
     * The corresponding table field name for the upload.
     *
     * @var string
     */
    protected $field;

    /**
     * The config options from config/upload.php
     *
     * @var array
     */
    protected $config;

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
     * Flag to determine if only a basic upload will happen for the uploaded file, or the full process.
     * To specify if a simple upload should occur, just don't specify the $model and $field in the constructor.
     *
     * Basic upload consists only of:
     * uploading the original file to disk;
     *
     * Full upload consist of:
     * uploading the original file to disk;
     * generating styles for an uploaded image if enabled in config;
     * generating thumbnails for an uploaded video if enabled in config;
     * saving the upload to database if enabled in config;
     * removing old uploads both from database and storage if enabled in config;
     *
     * @var bool
     */
    protected $simple = false;

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
        'raw',
        'sln',
        'tta',
        'vox',
        'wma',
        'wv',
    ];

    /**
     * Build a fully configured UploadService instance.
     *
     * @param UploadedFile|array|string $file
     * @param Model $model
     * @param string $field
     */
    public function __construct($file, Model $model = null, $field = null)
    {
        if ($model === null && $field === null) {
            $this->simple = true;
        }

        $this->setFile($file)->setField($field)->setModel($model)->setConfig($model);
        $this->setDisk()->setPath()->setName()->setExtension()->setSize()->setType();
    }

    /**
     * Set the file to work with.
     *
     * @param UploadedFile|array|string $file
     * @return $this
     */
    public function setFile($file)
    {
        switch ($file) {
            case is_string($file):
                $this->file = $this->createFromString($file);
                break;
            case is_array($file):
                $this->file = $this->createFromArray($file);
                break;
            default:
                $this->file = $this->createFromObject($file);
        }

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
     * Set the model class to work with.
     *
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model = null)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the model class.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the field name to work with.
     *
     * @param string $field
     * @return $this
     */
    public function setField($field = null)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get the field name.
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set the appropriate config to work with.
     * The config here will be fully/partially overwritten by the getUploadConfig() method from child model class.
     *
     * @param Model $model
     * @return $this
     */
    public function setConfig(Model $model = null)
    {
        if (method_exists($model, 'getUploadConfig')) {
            $this->config = array_replace_recursive(config('upload'), $model->getUploadConfig());
        } else {
            $this->config = config('upload');
        }

        return $this;
    }

    /**
     * Get the concatenated configuration for this particular upload.
     *
     * @param string|null $key
     * @return Model
     */
    public function getConfig($key = null)
    {
        return Arr::get($this->config, $key);
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
        if ($this->hasOriginal()) {
            $this->name = $this->original->name;
        } else {
            $this->name = str_random(40) . '.' . $this->file->getClientOriginalExtension();

            if (Storage::disk($this->disk)->exists($this->path . '/' . $this->name)) {
                $this->setName();
            }
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
        if ($this->hasOriginal()) {
            $this->path = $this->original->path;
        } else {
            $this->path = date('Y') . '/' . date('m') . '/' . date('j');
        }

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
        if ($this->hasOriginal()) {
            $this->extension = $this->original->extension;
        } else {
            $this->extension = strtolower($this->file->getClientOriginalExtension());
        }

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
        if ($this->hasOriginal()) {
            $this->size = $this->original->size;
        } else {
            $this->size = $this->file->getClientSize();
        }

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
     * @return $this
     */
    public function setType()
    {
        if ($this->hasOriginal()) {
            $this->type = $this->original->type;
        } else {
            switch ($this) {
                case $this->isImage():
                    $this->type = self::TYPE_IMAGE;
                    break;
                case $this->isVideo():
                    $this->type = self::TYPE_VIDEO;
                    break;
                case $this->isAudio():
                    $this->type = self::TYPE_AUDIO;
                    break;
                case $this->isFile():
                    $this->type = self::TYPE_FILE;
                    break;
            }
        }

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
     * Set the original loaded model for the file.
     *
     * @param Upload|string|null $file
     * @throws UploadException
     */
    public function setOriginal($file = null)
    {
        try {
            $this->original = $file instanceof Upload ? $file : Upload::whereFullPath($file)->firstOrFail();
        } catch (Exception $e) {
            throw new UploadException(
                'Original upload could not be found!'
            );
        }
    }

    /**
     * Get the original loaded model for the file.
     *
     * @return Upload
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * Check if upload/unload is attempted on an already existing file.
     * File should exist both in storage and in database.
     *
     * @return bool
     */
    public function hasOriginal()
    {
        return
            ($this->getOriginal() instanceof Upload && $this->getOriginal()->exists) &&
            Storage::disk($this->getDisk())->exists($this->getOriginal()->full_path);
    }

    /**
     * Establish if only a basic upload will happen for the uploaded file, or the full process.
     *
     * @return bool
     */
    public function isSimpleUpload()
    {
        return $this->simple === true;
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
     * Manage uploading of files.
     *
     * The uploading will be done based on the file type: image|video|audio|file.
     * Every uploading type has custom logic applicable only for that type.
     *
     * If saving to database is enabled in config/upload.php, that will be done too.
     * If forgetting old uploads is enabled in config/upload.php, that will be done too.
     *
     * If anything fails with the uploading process, restore everything and throw a specific error.
     *
     * @return string
     * @throws UploadException
     */
    public function upload()
    {
        try {
            switch ($this->getFile()) {
                case $this->isImage():
                    $this->storeImageToDisk();
                    break;
                case $this->isVideo():
                    $this->storeVideoToDisk();
                    break;
                case $this->isAudio():
                    $this->storeAudioToDisk();
                    break;
                case $this->isFile():
                    $this->storeFileToDisk();
                    break;
            }

            if (!$this->hasOriginal()) {
                $this->saveUploadToDatabase();
            }

            if (!$this->isSimpleUpload()) {
                $this->forgetOldUpload();
            }

            return $this;
        } catch (UploadException $e) {
            if (!$this->hasOriginal()) {
                $this->removeUploadFromDisk();
            }

            throw new UploadException($e->getMessage());
        }
    }

    /**
     * Manage deleting and removing files.
     *
     * Remove the original file and all it's dependencies from storage.
     * Also, if saving to database is enabled in config/upload.php, the given file will be removed from database too.
     *
     * To apply this method properly, pass in this class' constructor, just the first parameter.
     * The parameter's value should be the full path of an existing file in the database's table set in config/upload.php
     *
     * You also have the possibility to overlook deleting the following:
     * - the database record
     * - the original file from disk
     * - the generated file's thumbnails from disk
     *
     * @throws Exception
     * @throws UploadException
     */
    public function unload()
    {
        try {
            if (config('upload.database.save') === true) {
                $this->getOriginal()->delete();
            }

            $matchingFiles = preg_grep(
                '~^' . $this->getOriginal()->path . '/' . substr($this->getOriginal()->name, 0, strpos($this->getOriginal()->name, '.')) . '.*~',
                Storage::disk(config('upload.storage.disk'))->files($this->getOriginal()->path)
            );

            foreach ($matchingFiles as $file) {
                Storage::disk(config('upload.storage.disk'))->delete($file);
            }
        } catch (UploadException $e) {
            throw new UploadException($e->getMessage());
        }
    }

    /**
     * Download an existing file by it's full path.
     *
     * To apply this method properly, pass in this class' constructor, just the first parameter.
     * The parameter's value should be the full path of an existing file in the database's table set in config/upload.php
     *
     * @return BinaryFileResponse
     * @throws UploadException
     */
    public function download()
    {
        try {
            return response()->download(
                Storage::disk($this->getDisk())->getDriver()->getAdapter()->applyPathPrefix($this->getOriginal()->full_path)
            );
        } catch (UploadException $e) {
            throw new UploadException($e->getMessage());
        }
    }

    /**
     * Display an existing file by it's full path.
     *
     * To apply this method properly, pass in this class' constructor, just the first parameter.
     * The parameter's value should be the full path of an existing file in the database's table set in config/upload.php
     *
     * @return BinaryFileResponse
     * @throws UploadException
     */
    public function show()
    {
        try {
            return response()->file(
                Storage::disk($this->getDisk())->getDriver()->getAdapter()->applyPathPrefix($this->getOriginal()->full_path)
            );
        } catch (UploadException $e) {
            throw new UploadException($e->getMessage());
        }
    }

    /**
     * Store to disk a specific 'image' file type.
     *
     * @return false|string
     * @throws UploadException
     */
    protected function storeImageToDisk()
    {
        $this->guardAgainstMaxSize('images');
        $this->guardAgainstAllowedExtensions('images');

        return $this->attemptStoringToDisk(function () {
            $image = $this->storeToDisk();

            if (!$this->hasOriginal()) {
                $this->generateThumbnailForImage($image);
            }

            if (!$this->isSimpleUpload()) {
                $this->generateStylesForImage($image);
            }

            return $image;
        });
    }

    /**
     * Store to disk a specific 'video' file type.
     *
     * @return false|string
     * @throws UploadException
     */
    protected function storeVideoToDisk()
    {
        $this->guardAgainstMaxSize('videos');
        $this->guardAgainstAllowedExtensions('videos');

        return $this->attemptStoringToDisk(function () {
            set_time_limit(3600);
            ini_set('max_execution_time', 3600);

            $video = $this->hasOriginal() ? $this->getOriginal()->full_path : $this->storeToDisk();

            if (!$this->hasOriginal()) {
                $this->generateThumbnailsForVideo($video);
            }

            if (!$this->isSimpleUpload()) {
                $this->generateStylesForVideo($video);
            }

            return $video;
        });

    }

    /**
     * Store to disk a specific 'audio' file type.
     *
     * @return false|string
     * @throws UploadException
     */
    protected function storeAudioToDisk()
    {
        $this->guardAgainstMaxSize('audios');
        $this->guardAgainstAllowedExtensions('audios');

        return $this->attemptStoringToDisk(function () {
            return $this->hasOriginal() ? $this->getOriginal()->full_path : $this->storeToDisk();
        });

    }

    /**
     * Store to disk a specific 'file' file type.
     *
     * @return false|string
     * @throws UploadException
     */
    protected function storeFileToDisk()
    {
        $this->guardAgainstMaxSize('files');
        $this->guardAgainstAllowedExtensions('files');

        return $this->attemptStoringToDisk(function () {
            return $this->hasOriginal() ? $this->getOriginal()->full_path : $this->storeToDisk();
        });
    }

    /**
     * Simply upload (store) the given file.
     * When uploading, use the generated file name and file path.
     * The file will be stored on the disk provided in the config/upload.php file.
     *
     * IMPORTANT:
     * If an upload from an existing file is amended, the uploader will just return the original file instance.
     * This way, duplicating files on disk is avoided.
     *
     * @return false|string
     */
    protected function storeToDisk()
    {
        if ($this->hasOriginal()) {
            return $this->getOriginal()->full_path;
        }

        return $this->getFile()->storePubliclyAs(
            $this->getPath(), $this->getName(), $this->getDisk()
        );
    }

    /**
     * @param Closure $callback
     * @return mixed
     * @throws UploadException
     */
    protected function attemptStoringToDisk(Closure $callback)
    {
        try {
            $upload = call_user_func($callback);

            if (!$upload) {
                throw new UploadException(
                    'Failed uploading file(s)! Please try again.'
                );
            }
        } catch (Exception $e) {
            throw new UploadException(
                $e instanceof UploadException ? $e->getMessage() :
                    'Something went wrong when attempting the upload! Please try again.'
            );
        }

        return $upload;
    }

    /**
     * Save details about the newly uploaded file into the database.
     * The details will be saved into the corresponding uploads database column.
     * The table where to save the file's details, can be set in config/upload.php -> database.table key.
     * Please note that the saving will be made only if the database.save key is set to true.
     *
     * @throws UploadException
     */
    protected function saveUploadToDatabase()
    {
        if ($this->getConfig('database.save') !== true) {
            return;
        }

        try {
            $data = [
                'name' => $this->getName(),
                'original_name' => $this->getFile()->getClientOriginalName(),
                'path' => $this->getPath(),
                'full_path' => $this->getPath() . '/' . $this->getName(),
                'extension' => $this->getExtension(),
                'size' => $this->getSize(),
                'mime' => $this->getFile()->getMimeType(),
                'type' => $this->getType(),
            ];

            $validator = Validator::make(
                $data, (new UploadRequest)->rules()
            );

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            Upload::create($data);
        } catch (ValidationException $e) {
            throw new UploadException(
                'Uploader did not pass the validation system! The uploaded file might be corrupted.'
            );
        } catch (Exception $e) {
            throw new UploadException(
                'Failed saving the uploaded file to the database! Please try again.'
            );
        }
    }

    /**
     * Remove a previously stored uploaded file from disk.
     * Also remove it's dependencies (thumbnails, additional styles, etc.).
     *
     * @return void
     */
    protected function removeUploadFromDisk()
    {
        $matchingFiles = preg_grep(
            '~^' . $this->getPath() . '/' . substr($this->getName(), 0, strpos($this->getName(), '.')) . '.*~',
            Storage::disk($this->getDisk())->files($this->getPath())
        );

        foreach ($matchingFiles as $file) {
            Storage::disk($this->getDisk())->delete($file);
        }
    }

    /**
     * Try removing old upload from disk when uploading a new one.
     *
     * @throws UploadException
     */
    protected function forgetOldUpload()
    {
        if ($this->getConfig('storage.keep_old') === true) {
            return;
        }

        $oldFile = last(explode('/', $this->getModel()->getOriginal($this->getField())));

        if (!$oldFile) {
            return;
        }

        $matchingFiles = preg_grep(
            '~^' . $this->getPath() . '/' . substr($oldFile, 0, strpos($oldFile, '.')) . '.*~',
            Storage::disk($this->getDisk())->files($this->getPath())
        );

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            Upload::where([
                'full_path' => $this->getModel()->getOriginal($this->getField())
            ])->delete();

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            foreach ($matchingFiles as $file) {
                Storage::disk($this->getDisk())->delete($file);
            }
        } catch (Exception $e) {
            throw new UploadException(
                'Failed removing old uploads from disk and/or database! Please try again.'
            );
        }
    }

    /**
     * Try generating thumbnail for the original uploaded image.
     * The thumbnail generation flag and size are defined in the config/upload.php (images -> generate_thumbnail | thumbnail_style).
     * Also, when creating the image thumbnail, the "quality" configuration option is taken into consideration.
     *
     * @param string $path
     * @throws UploadException
     */
    protected function generateThumbnailForImage($path)
    {
        if (!$this->getConfig('images.generate_thumbnail')) {
            return;
        }

        if (!Storage::disk($this->getDisk())->exists($path)) {
            throw new UploadException(
                'Could not create image thumbnail because the image "' . $path . '" does not exist!'
            );
        }

        try {
            $original = Storage::disk($this->getDisk())->get($path);
            $width = (int)$this->getConfig('images.thumbnail_style.width') ?: 100;
            $height = (int)$this->getConfig('images.thumbnail_style.height') ?: 100;

            Storage::disk($this->getDisk())->put(
                $this->getPath() . '/' . substr_replace($this->getName(), '_thumbnail', strpos($this->getName(), '.' . $this->getExtension()), 0),
                Image::make($original)->fit($width, $height)->stream(null, (int)$this->getConfig('images.quality') ?: 90)->__toString()
            );
        } catch (Exception $e) {
            throw new UploadException(
                'Thumbnail generation for the uploaded image failed! Please try again.'
            );
        }
    }

    /**
     * Try generating styles for the original uploaded image.
     * The styles are defined in the config/upload.php (images -> styles), or overwritten in the model via the getUploadConfig() method.
     * Also, when creating the styles, the "quality" configuration option is taken into consideration.
     *
     * @param string $path
     * @throws UploadException
     */
    protected function generateStylesForImage($path)
    {
        if (!$this->getConfig('images.styles')) {
            return;
        }

        if (!Storage::disk($this->getDisk())->exists($path)) {
            throw new UploadException(
                'Could not create image styles because the image "' . $path . '" does not exist!'
            );
        }

        try {
            $original = Storage::disk($this->getDisk())->get($path);

            foreach ($this->getConfig('images.styles') as $field => $styles) {
                if ($field != $this->getField()) {
                    continue;
                }

                foreach ($styles as $name => $style) {
                    Storage::disk($this->getDisk())->put(
                        $this->getPath() . '/' . substr_replace($this->getName(), '_' . $name, strpos($this->getName(), '.' . $this->getExtension()), 0),
                        Image::make($original)->{!isset($style['ratio']) || $style['ratio'] === true ? 'fit' : 'resize'}($style['width'], $style['height'])
                            ->stream(null, (int)$this->getConfig('images.quality') ?: 90)->__toString()
                    );
                }
            }
        } catch (Exception $e) {
            throw new UploadException(
                'Styles generation for the uploaded image failed! Please try again.'
            );
        }
    }

    /**
     * Try generating the video thumbnails.
     * The generation is done according to the config properties from config/upload.php:
     * videos.generate_thumbnails and videos.thumbnails_number
     *
     * @param string $path
     * @throws UploadException
     */
    protected function generateThumbnailsForVideo($path)
    {
        $generate = $this->getConfig('videos.generate_thumbnails');
        $number = (int)$this->getConfig('videos.thumbnails_number');

        if (!$generate || !$number) {
            return;
        }

        try {
            $video = FFMpeg::fromDisk($this->getDisk())->open($path);
            $duration = $video->getDurationInSeconds();

            for ($i = 1; $i <= $number; $i++) {
                $thumbnail = str_replace('.' . $this->getExtension(), '', $path) . '_thumbnail_' . $i . '.jpg';

                $video
                    ->getFrameFromSeconds(floor(($duration * $i) / $number))
                    ->export()->toDisk($this->getDisk())->save($thumbnail);
            }
        } catch (Exception $e) {
            throw new UploadException(
                'Thumbnail generation for the uploaded video failed! Please try again.'
            );
        }
    }

    /**
     * Try generating styles for the original uploaded video.
     * The styles are defined in the config/upload.php (videos -> styles), or overwritten in the model via the getUploadConfig() method.
     * Also, when creating the styles, please keep in mind that the newly generated videos will be WebM encoded for web pages.
     *
     * @param string $path
     * @throws UploadException
     */
    protected function generateStylesForVideo($path)
    {
        if (!$this->getConfig('videos.styles')) {
            return;
        }

        if (!Storage::disk($this->getDisk())->exists($path)) {
            throw new UploadException(
                'Could not create video styles because the video "' . $path . '" does not exist!'
            );
        }

        try {
            $original = FFMpeg::fromDisk($this->getDisk())->open($path);

            foreach ($this->getConfig('videos.styles') as $field => $styles) {
                if ($field != $this->getField()) {
                    continue;
                }

                foreach ($styles as $name => $style) {
                    $original->addFilter(function ($filters) use ($style) {
                        $filters->resize(new FFMpeg\Coordinate\Dimension($style['width'], $style['height']));
                    })->export()->toDisk($this->getDisk())->inFormat(new FFMpeg\Format\Video\WebM)->save(
                        $this->getPath() . '/' . substr_replace($this->getName(), '_' . $name, strpos($this->getName(), '.' . $this->getExtension()), 0)
                    );
                }
            }
        } catch (Exception $e) {
            throw new UploadException(
                'Styles generation for the uploaded image failed! Please try again.'
            );
        }
    }

    /**
     * Verify if the uploaded file's size is bigger than the maximum size allowed.
     * The maximum size allowed is specified in config/upload.php -> images|videos|audios|files.max_size
     *
     * @param string $type
     * @throws UploadException
     */
    protected function guardAgainstMaxSize($type)
    {
        $size = (float)$this->getConfig($type . '.max_size');

        if (!$size) {
            return;
        }

        if ($size * pow(1024, 2) < $this->getSize()) {
            throw new UploadException(
                'The uploaded "' . $type . '" size exceeds the maximum allowed for audio files. (' . $size . 'MB)'
            );
        }
    }

    /**
     * Verify if the uploaded file's extension matches the allowed file extensions.
     * The allowed file extensions are specified in config/upload.php -> images|videos|audios|files.allowed_extensions
     *
     * @param string $type
     * @throws UploadException
     */
    protected function guardAgainstAllowedExtensions($type)
    {
        $allowed = $this->getConfig($type . '.allowed_extensions');

        if (!$allowed) {
            return;
        }

        $extensions = is_array($allowed) ? $allowed : explode(',', $allowed);

        if (!in_array($this->getExtension(), array_map('strtolower', $extensions))) {
            throw new UploadException(
                'The "' . $type . '" extension is not allowed! The extensions allowed are: ' . implode(', ', $extensions)
            );
        }
    }

    /**
     * Get the UploadedFile instance.
     *
     * @param object $file
     * @return UploadedFile
     * @throws UploadException
     */
    private function createFromObject($file)
    {
        if (!($file instanceof UploadedFile)) {
            throw new UploadException('Invalid file!');
        }

        return $file;
    }

    /**
     * Create an UploadedFile instance from an array. ($_FILES)
     *
     * @param array $file
     * @return UploadedFile
     * @throws UploadException
     */
    private function createFromArray(array $file = [])
    {
        if (!isset($file['name']) || !isset($file['tmp_name']) || !isset($file['type']) || !isset($file['size']) || !isset($file['error'])) {
            throw new UploadException('Invalid file!');
        }

        return new UploadedFile(
            $file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']
        );
    }

    /**
     * Create an UploadedFile instance from a string.
     *
     * @param string $file
     * @return UploadedFile
     * @throws UploadException
     */
    private function createFromString($file = '')
    {
        try {
            return $this->createFromExisting($file);
        } catch (Exception $e) {
            if (filter_var($file, FILTER_VALIDATE_URL)) {
                return $this->createFromUrl($file);
            }

            throw new UploadException('Invalid file!');
        }
    }

    /**
     * Create an UploadedFile instance from an already existing upload's full path.
     *
     * @param string $file
     * @return UploadedFile
     * @throws UploadException
     */
    private function createFromExisting($file = '')
    {
        try {
            $this->setOriginal($file);

            $disk = config('upload.storage.disk');
            $path = config('filesystems.disks.' . $disk . '.root') . DIRECTORY_SEPARATOR;

            return new UploadedFile(
                $path . $this->getOriginal()->full_path,
                $this->getOriginal()->original_name,
                $this->getOriginal()->mime,
                $this->getOriginal()->size
            );
        } catch (Exception $e) {
            throw new UploadException('Invalid file!');
        }
    }

    /**
     * Create an UploadedFile instance from a URL.
     *
     * @param string $file
     * @return UploadedFile
     * @throws UploadException
     */
    private function createFromUrl($file = '')
    {
        try {
            $ch = curl_init($file);

            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $raw = curl_exec($ch);
            $size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

            curl_close($ch);

            if (strpos($file, '?') !== false) {
                list($file, $query) = explode('?', $file);
            }

            $info = pathinfo($file);
            $name = $info['basename'];
            $path = sys_get_temp_dir() . '/' . $name;

            file_put_contents($path, $raw);

            $mime = MimeTypeGuesser::getInstance()->guess($path);

            if (empty($info['extension'])) {
                $extension = (new MimeTypeExtensionGuesser())->guess($mime);

                unlink($path);
                $path = sys_get_temp_dir(). '/' . $name. '.' . $extension;
                file_put_contents($path, $raw);
            }

            return new UploadedFile($path, $name, $mime, $size);
        } catch (Exception $e) {
            throw new UploadException('Invalid file!');
        }
    }
}