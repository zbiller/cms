<?php

namespace App\Models\Backup;

use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasActivity;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;

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
     * The attributes that mass assignable.
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