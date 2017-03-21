<?php

namespace App\Services;

use DB;
use Storage;
use Image;
use Closure;
use Exception;
use FFMpeg;
use Carbon\Carbon;
use App\Models\Model;
use App\Configs\UploadConfig;
use App\Exceptions\UploadException;
use Illuminate\Http\UploadedFile;

class UploadService
{
    /**
     * The corresponding table field name for the upload.
     *
     * @var string
     */
    protected $field;

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
     * Resolve dependencies automatically.
     * In order for this to happen, don't instantiate this normally using "new".
     * Use app(Upload::class) instead.
     *
     * @param string $field
     * @param UploadedFile $file
     * @param Model $model
     * @param UploadConfig $config
     */
    public function __construct($field, UploadedFile $file, Model $model, UploadConfig $config)
    {
        $this->setField($field)->setFile($file)->setModel($model)->setConfig($config);
        $this->setDisk()->setPath()->setName()->setExtension()->setSize();
    }

    /**
     * Set the field name to work with.
     *
     * @param string $field
     * @return $this
     */
    public function setField($field)
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
     * Set the model class to work with.
     *
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model)
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
     * Set the appropriate config to work with.
     * The config here will be fully/partially overwritten by the getUploadConfig() method from child model class.
     *
     * @param UploadConfig $config
     * @return $this
     */
    public function setConfig(UploadConfig $config)
    {
        $this->config = $config->config;

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
        if (!$key) {
            return $this->config;
        }

        if (str_contains($key, '.')) {
            return eval(
                'return $this->config["' . implode('"]["', explode('.', $key)) . '"];'
            );
        }

        return $this->config[$key];
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
        $this->extension = strtolower($this->file->getClientOriginalExtension());

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

            $this->saveUploadToDatabase();
            $this->forgetOldUpload();

            return $this->getName();
        } catch (UploadException $e) {
            $this->removeUploadFromDisk();

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
        $this->setType(self::TYPE_IMAGE);

        $this->guardAgainstMaxSize('images');
        $this->guardAgainstAllowedExtensions('images');

        return $this->attemptStoringToDisk(function () {
            $image = $this->storeToDisk();

            $this->generateStylesForImage($image);

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
        $this->setType(self::TYPE_VIDEO);

        $this->guardAgainstMaxSize('videos');
        $this->guardAgainstAllowedExtensions('videos');

        return $this->attemptStoringToDisk(function () {
            $video = $this->storeToDisk();

            $this->generateThumbnailsForVideo($video);

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
        $this->setType(self::TYPE_AUDIO);

        $this->guardAgainstMaxSize('audios');
        $this->guardAgainstAllowedExtensions('audios');

        return $this->attemptStoringToDisk(function () {
            return $this->storeToDisk();
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
        $this->setType(self::TYPE_FILE);

        $this->guardAgainstMaxSize('files');
        $this->guardAgainstAllowedExtensions('files');

        return $this->attemptStoringToDisk(function () {
            return $this->storeToDisk();
        });
    }

    /**
     * Simply upload (store) the given file.
     * When uploading, use the generated file name and file path.
     * The file will be stored on the disk provided in the config/upload.php file.
     *
     * @return false|string
     */
    protected function storeToDisk()
    {
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
                $e instanceof UploadException ?
                    $e->getMessage() :
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
     * @return bool
     * @throws UploadException
     */
    protected function saveUploadToDatabase()
    {
        if ($this->getConfig('database.save') !== true) {
            return true;
        }

        try {
            $result = DB::table($this->getConfig('database.table'))->insert([
                'name' => $this->getName(),
                'original_name' => $this->getFile()->getClientOriginalName(),
                'path' => $this->getPath(),
                'full_path' => $this->getPath() . '/' . $this->getName(),
                'extension' => $this->getExtension(),
                'size' => $this->getSize(),
                'mime' => $this->getFile()->getMimeType(),
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
     * Try removing old
     *
     * @throws UploadException
     */
    protected function forgetOldUpload()
    {
        if ($this->getConfig('storage.keep_old') === true) {
            return true;
        }

        $oldFile = $this->getModel()->getOriginal($this->getField());

        if (!$oldFile) {
            return true;
        }

        $matchingFiles = preg_grep(
            '~^' . $this->getPath() . '/' . substr($oldFile, 0, strpos($oldFile, '.')) . '.*~',
            Storage::disk($this->getDisk())->files($this->getPath())
        );

        try {
            DB::table($this->getConfig('database.table'))->where('name', '=', $oldFile)->delete();

            foreach ($matchingFiles as $file) {
                Storage::disk($this->getDisk())->delete($file);
            }

            return true;
        } catch (Exception $e) {
            throw new UploadException(
                'Failed removing old uploads from disk and/or database! Please try again.'
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
        try {
            if (!Storage::disk($this->getDisk())->exists($path)) {
                throw new UploadException(
                    'Could not create image styles because the file ' . $path . ' does not exist!'
                );
            }

            $original = Storage::disk($this->getDisk())->get($path);

            foreach ($this->getConfig('images.styles') as $field => $styles) {
                if ($field == $this->getField()) {
                    foreach ($styles as $name => $style) {
                        $styleName = $this->getPath() . '/' . substr_replace($this->getName(), '_' . $name, strpos($this->getName(), '.' . $this->getExtension()), 0);
                        $styleImage = Image::make($original);

                        if (!isset($style['ratio']) || $style['ratio'] === true) {
                            $styleImage->fit($style['width'], $style['height']);
                        } else {
                            $styleImage->resize($style['width'], $style['height']);
                        }

                        Storage::disk($this->getDisk())->put(
                            $styleName, $styleImage->stream(null, (int)$this->getConfig('images.quality') ?: 90)->__toString()
                        );
                    }
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
        try {
            $generateThumbnails = $this->getConfig('videos.generate_thumbnails');
            $thumbnailsNumber = (int)$this->getConfig('videos.thumbnails_number');

            if ($generateThumbnails === true && $thumbnailsNumber > 0) {
                $uploadedVideo = FFMpeg::fromDisk($this->getDisk())->open($path);
                $videoDuration = $uploadedVideo->getDurationInSeconds();

                for ($i = 1; $i <= $thumbnailsNumber; $i++) {
                    $thumbnailName = str_replace('.' . $this->getExtension(), '', $path) . '_thumbnail_' . $i . '.jpg';

                    $uploadedVideo
                        ->getFrameFromSeconds(floor(($videoDuration * $i) / $thumbnailsNumber))
                        ->export()->toDisk($this->getDisk())->save($thumbnailName);
                }
            }
        } catch (Exception $e) {
            throw new UploadException(
                'Thumbnail generation for the uploaded video failed! Please try again.'
            );
        }
    }

    /**
     * Verify if the uploaded file's size is bigger than the maximum size allowed.
     * The maximum size allowed is specified in config/upload.php -> images|videos|audios|files.max_size
     *
     * @param string $type
     * @return bool
     * @throws UploadException
     */
    protected function guardAgainstMaxSize($type)
    {
        $maxSize = (float)$this->getConfig($type . '.max_size');

        if ($maxSize > 0 && $maxSize * pow(1024, 2) < $this->getSize()) {
            throw new UploadException(
                "The uploaded {$type} size exceeds the maximum allowed for audio files. ({$maxSize}MB)"
            );
        }

        return true;
    }

    /**
     * Verify if the uploaded file's extension matches the allowed file extensions.
     * The allowed file extensions are specified in config/upload.php -> images|videos|audios|files.allowed_extensions
     *
     * @param string $type
     * @return bool
     * @throws UploadException
     */
    protected function guardAgainstAllowedExtensions($type)
    {
        $allowedExtensions = $this->getConfig($type . '.allowed_extensions');

        if ($allowedExtensions) {
            $extensions = is_array($allowedExtensions) ? $allowedExtensions : explode(',', $allowedExtensions);

            if (!in_array($this->getExtension(), array_map('strtolower', $extensions))) {
                throw new UploadException(
                    "The {$type} extension is not allowed! The extensions allowed are: " . implode(', ', $extensions)
                );
            }
        }

        return true;
    }
}