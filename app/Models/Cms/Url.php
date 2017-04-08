<?php

namespace App\Models\Cms;

use App\Models\Model;

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
}