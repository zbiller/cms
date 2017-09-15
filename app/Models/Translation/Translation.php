<?php

namespace App\Models\Translation;

use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;

class Translation extends Model
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
    protected $table = 'translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
    ];

    /**
     * Get the locale attribute pretty formatted.
     *
     * @return string
     */
    public function getLocaleFormattedAttribute()
    {
        return strtoupper($this->attributes['locale']);
    }

    /**
     * Get the group attribute pretty formatted.
     *
     * @return string
     */
    public function getGroupFormattedAttribute()
    {
        return title_case($this->attributes['group']);
    }

    /**
     * Get the key attribute pretty formatted.
     *
     * @return string
     */
    public function getKeyFormattedAttribute()
    {
        return title_case($this->attributes['key']);
    }

    /**
     * Filter the query to show only results belonging to a translation group.
     *
     * @param Builder $query
     * @param string $group
     * @return mixed
     */
    public function scopeOfTranslatedGroup($query, $group)
    {
        return $query->where('group', $group)->whereNotNull('value');
    }

    /**
     * Sort the results alphabetically by group key and then by individual key.
     *
     * @param Builder $query
     * @return mixed
     */
    public function scopeOrderByGroupKeys($query)
    {
        return $query->orderBy('group')->orderBy('key');
    }

    /**
     * Select all distinct translation groups.
     *
     * @param Builder $query
     * @return mixed
     */
    public function scopeDistinctGroup($query)
    {
        return $query->select('group')->distinct();
    }

    /**
     * Set the options for the HasActivityLog trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('key');
    }
}