<?php

namespace App\Models\Location;

use App\Models\Model;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use App\Options\ActivityOptions;
use Illuminate\Database\Eloquent\Builder;

class Country extends Model
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
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Country has many states.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states()
    {
        return $this->hasMany(State::class, 'country_id')->orderBy('name');
    }

    /**
     * Country has many cities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        return $this->hasMany(City::class, 'country_id')->orderBy('name');
    }

    /**
     * Sort the query with newest records first.
     *
     * @param Builder $query
     */
    public function scopeNewest($query)
    {
        $query->orderBy('created_at', 'desc');
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('name');
    }
}