<?php

namespace App\Models\Upload;

use App\Models\Model;
use App\Options\ActivityOptions;
use App\Services\UploadService;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Schema\Blueprint;

class Upload extends Model
{
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table;

    /**
     * The attributes that are mass assignable.
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
     * @return bool
     */
    public function isImage()
    {
        return $this->type == UploadService::TYPE_IMAGE;
    }

    /**
     * @return bool
     */
    public function isVideo()
    {
        return $this->type == UploadService::TYPE_VIDEO;
    }

    /**
     * @return bool
     */
    public function isAudio()
    {
        return $this->type == UploadService::TYPE_AUDIO;
    }

    /**
     * @return bool
     */
    public function isFile()
    {
        return !$this->isImage() && !$this->isVideo() && !$this->isAudio();
    }

    /**
     * Get the size in megabytes.
     *
     * @return string
     */
    public function getHelperAttribute()
    {
        return uploaded($this->attributes['full_path']);
    }

    /**
     * Get the size in megabytes.
     *
     * @return string
     */
    public function getSizeInMbAttribute()
    {
        return number_format($this->attributes['size'] / pow(1024, 2), 2);
    }

    /**
     * Get the size in megabytes.
     *
     * @return string
     */
    public function getTypeIconAttribute()
    {
        return asset('/images/admin/' . strtolower(self::$types[$this->type]) . '-icon.png');
    }

    /**
     * Filter query results to show uploads only of type.
     * Param $types: single upload type as string or multiple upload types as an array.
     *
     * @param Builder $query
     * @param ...$types
     */
    public function scopeOnlyTypes($query, ...$types)
    {
        $types = array_flatten($types);

        if (!empty($types)) {
            $query->where(function ($q) use ($types) {
                foreach ($types as $type) {
                    if ($type) {
                        $q->orWhere('type', is_numeric($type) ? $type : array_search(title_case($type), self::$types));
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads excluding the given types.
     * Param $types: single upload type as string or multiple upload types as an array.
     *
     * @param Builder $query
     * @param ...$types
     */
    public function scopeExcludingTypes($query, ...$types)
    {
        $types = array_flatten($types);

        if (!empty($types)) {
            $query->where(function ($q) use ($types) {
                foreach ($types as $type) {
                    if ($type) {
                        $q->where('type', '!=', is_numeric($type) ? $type : array_search(title_case($type), self::$types));
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads only of the given extensions.
     * Param $extensions: single upload extension as string or multiple upload extensions as an array.
     *
     * @param Builder $query
     * @param ...$extensions
     */
    public function scopeOnlyExtensions($query, ...$extensions)
    {
        $extensions = array_flatten($extensions);

        if (!empty($extensions)) {
            $query->where(function ($q) use ($extensions) {
                foreach ($extensions as $extension) {
                    if ($extension) {
                        $q->orWhere('extension', strtolower($extension));
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads excluding the given extensions.
     * Param $extensions: single upload extension as string or multiple upload extensions as an array.
     *
     * @param Builder $query
     * @param ...$extensions
     */
    public function scopeExcludingExtensions($query, ...$extensions)
    {
        $extensions = array_flatten($extensions);

        if (!empty($extensions)) {
            $query->where(function ($q) use ($extensions) {
                foreach ($extensions as $extension) {
                    if ($extension) {
                        $q->orWhere('extension', '!=', strtolower($extension));
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads only of the given mime types.
     * Param $mimes: single upload mime as string or multiple upload mimes as an array.
     *
     * @param Builder $query
     * @param ...$mimes
     */
    public function scopeOnlyMimes($query, ...$mimes)
    {
        $mimes = array_flatten($mimes);

        if (!empty($mimes)) {
            $query->where(function ($q) use ($mimes) {
                foreach ($mimes as $mime) {
                    if ($mime) {
                        $q->orWhere('mime', strtolower($mime));
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads excluding the given mimes.
     * Param $mimes: single upload mime as string or multiple upload mimes as an array.
     *
     * @param Builder $query
     * @param ...$mimes
     */
    public function scopeExcludingMimes($query, ...$mimes)
    {
        $mimes = array_flatten($mimes);

        if (!empty($mimes)) {
            $query->where(function ($q) use ($mimes) {
                foreach ($mimes as $mime) {
                    if ($mime) {
                        $q->orWhere('mime', '!=', strtolower($mime));
                    }
                }
            });
        }
    }

    /**
     * Filter query results to show uploads only between the given sizes.
     * Param $minSize: the minimum size in MB.
     * Param $maxSize: the maximum size in MB.
     *
     * @param Builder $query
     * @param int $minSize
     * @param int $maxSize
     */
    public function scopeSizeBetween($query, $minSize, $maxSize)
    {
        $query->whereBetween('size', [
            $minSize * pow(1024, 2),
            $maxSize * pow(1024, 2)
        ]);
    }

    /**
     * Filter query results to show uploads that match the search criteria.
     * Param $attributes: an array containing field => value.
     *
     * @param Builder $query
     * @param array $attributes
     */
    public function scopeLike($query, array $attributes = [])
    {
        if (!empty($attributes)) {
            $query->where(function ($q) use ($attributes) {
                foreach ($attributes as $field => $value) {
                    if ($value) {
                        $q->orWhere($field, 'like', '%' . $value . '%');
                    }
                }
            });
        }

    }

    /**
     * Filter query results to show only one upload by it's full path.
     * Param $path: the full path of an upload.
     *
     * @param Builder $query
     * @param string $path
     */
    public function scopeWhereFullPath($query, $path)
    {
        $query->where('full_path', '=', $path);
    }

    /**
     * Sort the query alphabetically by original_name.
     *
     * @param $query
     */
    public function scopeInAlphabeticalOrder($query)
    {
        $query->orderBy('original_name', 'asc');
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
        $table->foreign($name)
            ->references('full_path')
            ->on((new static)->getTable())
            ->onDelete('restrict');
    }

    /**
     * Set the options for the HasActivityLog trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('original_name');
    }
}