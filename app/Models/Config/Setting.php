<?php

namespace App\Models\Config;

use App\Models\Model;
use App\Traits\IsCacheable;
use Illuminate\Database\Eloquent\Builder;

class Setting extends Model
{
    use IsCacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Filter the query by key.
     *
     * @param Builder $query
     * @param string $key
     * @return mixed
     */
    public function scopeByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeInAlphabeticalOrder($query)
    {
        $query->orderBy('key', 'asc');
    }

    /**
     * Get the setting related to the specified key.
     *
     * @param string $key
     * @return mixed
     */
    public static function findByKey($key)
    {
        return static::byKey($key)->first();
    }
}