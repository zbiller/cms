<?php

namespace App\Models\Seo;

use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;

class Redirect extends Model
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
    protected $table = 'redirects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'old_url',
        'new_url',
        'status',
    ];

    /**
     * The constants defining the redirect status codes available.
     *
     * @const
     */
    const STATUS_NORMAL = 302;
    const STATUS_PERMANENT = 301;
    const STATUS_TEMPORARY = 307;

    /**
     * The property defining the redirect status codes available.
     *
     * @var array
     */
    public static $statuses = [
        self::STATUS_NORMAL => 'Normal (302)',
        self::STATUS_PERMANENT => 'Permanent (301)',
        self::STATUS_TEMPORARY => 'Temporary (307)',
    ];

    /**
     * Filter the query by an old url.
     *
     * @param Builder $query
     * @param string $url
     * @return mixed
     */
    public function scopeWhereOldUrl($query, $url)
    {
        return $query->where('old_url', $url);
    }

    /**
     * Filter the query by a new url.
     *
     * @param Builder $query
     * @param string $url
     * @return mixed
     */
    public function scopeWhereNewUrl($query, $url)
    {
        return $query->where('new_url', $url);
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeInAlphabeticalOrder($query)
    {
        $query->orderBy('old_url', 'asc');
    }

    /**
     * Return a valid redirect entity for a given path (old url).
     * A redirect is valid if:
     * - it has an url to redirect to (new url)
     * - it's status code is one of the statuses defined on this model
     *
     * @param string $path
     * @return Redirect|null
     */
    public static function findValidOrNull($path)
    {
        return static::where('old_url', trim($path, '/'))
            ->whereNotNull('new_url')->whereIn('status', array_keys(self::$statuses))->first();
    }

    /**
     * Set the options for the HasActivityLog trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('old_url');
    }
}