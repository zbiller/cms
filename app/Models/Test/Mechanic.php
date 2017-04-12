<?php

namespace App\Models\Test;

use App\Models\Model;

class Mechanic extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'cars_mechanics';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Mechanic has and belongs to many Cars.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function car()
    {
        return $this->belongsToMany(Car::class, 'cars_mechanics_ring', 'mechanic_id', 'car_id');
    }
}