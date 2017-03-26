<?php

namespace App\Models\Upload;

use App\Exceptions\UploadException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Storage;
use App\Models\Model;
use App\Services\UploadService;
use App\Traits\CanFilter;
use App\Traits\CanSort;
use Illuminate\Database\Schema\Blueprint;

class Upload extends Model
{
    use CanFilter;
    use CanSort;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table;

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'original_name',
        'path',
        'full_path',
        'extension',
        'size',
        'mime',
        'type',
    ];

    /**
     * @var UploadService
     */
    protected $upload;

    /**
     * Pretty formatted list of upload types.
     *
     * @var array
     */
    public static $types = [
        UploadService::TYPE_IMAGE => 'Image',
        UploadService::TYPE_VIDEO => 'Video',
        UploadService::TYPE_AUDIO => 'Audio',
        UploadService::TYPE_FILE => 'File',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('upload.database.table'));
    }

    /**
     * @param UploadedFile|string $upload
     * @return $this
     */
    public function withUpload($upload)
    {
        $this->upload = new UploadService($upload);

        return $this;
    }

    /**
     * @return $this
     */
    public function storeToDisk()
    {
        $this->upload->upload();

        return $this;
    }

    /**
     * @return $this
     * @throws UploadException
     */
    public function removeFromDisk()
    {
        $this->upload->removeUploadFromDisk();

        return $this;
    }

    /**
     * @return $this
     * @throws UploadException
     */
    public function saveToDatabase()
    {
        $this->upload->saveUploadToDatabase();

        return $this;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function deleteFromDatabase()
    {
        $this->delete();

        return $this;
    }









    /**
     * Remove all the loaded upload model's files from storage.
     *
     * @return $this
     */
    public function remove()
    {
        $matchingFiles = preg_grep(
            '~^' . $this->path . '/' . substr($this->name, 0, strpos($this->name, '.')) . '.*~',
            Storage::disk(config('upload.storage.disk'))->files($this->path)
        );

        foreach ($matchingFiles as $file) {
            Storage::disk(config('upload.storage.disk'))->delete($file);
        }

        return $this;
    }

    /**
     * Download the loaded upload model's original file.
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download()
    {
        return response()->download(
            Storage::disk(config('upload.storage.disk'))->getDriver()->getAdapter()->applyPathPrefix($this->full_path)
        );
    }

















    public function thumbnail()
    {
        return Storage::disk(config('upload.storage.disk'))->url(
            substr_replace($this->full_path, '_thumbnail', strpos($this->full_path, '.' . $this->extension), 0)
        );
    }

    /**
     * Create a fully qualified upload column in a database table.
     *
     * @param string $name
     * @param Blueprint $table
     */
    public static function column($name, Blueprint $table)
    {
        $table->string($name)->nullable();
        $table->foreign($name)->references('full_path')->on(config('upload.database.table'))->onDelete('restrict');
    }
}