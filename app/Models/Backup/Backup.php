<?php

namespace App\Models\Backup;

use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasActivity;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;
use Storage;

class Backup extends Model
{
    use HasActivity;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'backups';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path',
        'date',
        'size',
        'disk',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'date',
    ];

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
     * Filter the query by name.
     *
     * @param Builder $query
     * @param string $name
     * @return mixed
     */
    public function scopeWhereName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Filter the query by disk.
     *
     * @param Builder $query
     * @param string $disk
     * @return mixed
     */
    public function scopeWhereDisk($query, $disk)
    {
        return $query->where('disk', $disk);
    }

    /**
     * Sort the query ascending by size.
     *
     * @param Builder $query
     * @return mixed
     */
    public function scopeOrderBySizeAsc($query)
    {
        return $query->orderBy('size', 'asc');
    }

    /**
     * Sort the query descending by size.
     *
     * @param Builder $query
     * @return mixed
     */
    public function scopeOrderBySizeDesc($query)
    {
        return $query->orderBy('size', 'desc');
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeInAlphabeticalOrder($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Determine if the current backup is on the "local" filesystem driver.
     * Please note that we're talking about the filesystem "DRIVER", not the "DISK".
     *
     * @return bool
     */
    public function local()
    {
        return strtolower(config("filesystems.disks.{$this->disk}.driver")) === 'local';
    }

    /**
     * Download a backup zip archive from any storage driver.
     *
     * @return int|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download()
    {
        if ($this->local()) {
            return response()->download(
                Storage::disk($this->disk)->getDriver()->getAdapter()->applyPathPrefix($this->path)
            );
        } else {
            Storage::disk($this->disk)->setVisibility($this->path, 'public');

            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=" . basename($this->path));
            header("Content-Type: application/zip");

            $file = readfile(Storage::disk($this->disk)->url($this->path));

            Storage::disk($this->disk)->setVisibility($this->path, 'private');

            return $file;
        }
    }

    /**
     * Set the options for the HasActivityLog trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('path');
    }
}