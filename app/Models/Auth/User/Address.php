<?php

namespace App\Models\Auth\User;

use App\Models\Auth\User;
use App\Models\Location\City;
use App\Models\Location\Country;
use App\Models\Location\State;
use App\Models\Model;
use App\Options\ActivityOptions;
use App\Options\OrderOptions;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsOrderable;
use App\Traits\IsSortable;
use Illuminate\Database\Eloquent\Builder;

class Address extends Model
{
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use IsOrderable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'addresses';

    /**
     * The attributes that are protected against mass assign.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'country_id',
        'state_id',
        'city_id',
        'address',
    ];

    /**
     * The relations that are eager-loaded.
     *
     * @var array
     */
    protected $with = [
        'country',
        'state',
        'city',
    ];

    /**
     * Address belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Address belongs to a country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * Address belongs to a state.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    /**
     * Address belongs to a city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
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
        $query->orderBy('address', 'asc');
    }

    /**
     * Filter the query alphabetically by user.
     *
     * @param Builder $query
     * @param int $id
     */
    public function scopeWhereUser($query, $id)
    {
        $query->orderBy('user_id', $id);
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('address');
    }

    /**
     * Set the options for the IsOrderable trait.
     *
     * @return OrderOptions
     */
    public static function getOrderOptions()
    {
        return OrderOptions::instance();
    }
}