<?php

namespace App\Models\Test;

use App\Models\Model;

class Owner extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'cars_owners';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
    ];

    /**
     * Owner has many Cars.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cars()
    {
        return $this->hasMany(Car::class, 'owner_id');
    }

    public function getFullNameAttribute()
    {
        return implode(' ', [$this->attributes['first_name'], $this->attributes['last_name']]);
    }
}