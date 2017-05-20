<?php

namespace App\Models\Cms;

use App\Models\Model;
use Illuminate\Database\Eloquent\Builder;

class Url extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'urls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'urlable_id',
        'urlable_type',
    ];

    /**
     * Get all of the owning urlable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function urlable()
    {
        return $this->morphTo();
    }

    /**
     * @param Builder $query
     * @param string $url
     */
    public function scopeWhereUrl($query, $url)
    {
        $query->where('url', $url);
    }

    /**
     * @param Builder $query
     * @param int $id
     * @param string $type
     */
    public function scopeWhereUrlable($query, $id, $type)
    {
        $query->where([
            'urlable_id' => $id,
            'urlable_type' => $type,
        ]);
    }
}