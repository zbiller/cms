<?php

namespace App\Models\Upload;

use Storage;
use App\Models\Model;
use App\Services\UploadService;
use App\Traits\CanFilter;
use App\Traits\CanSort;
use Illuminate\Database\Eloquent\Builder;
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
     * @param Builder $query
     * @param $fullPath
     */
    public function scopeWhereFullPath($query, $fullPath)
    {
        $query->where('full_path', '=', $fullPath);
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