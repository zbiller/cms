<?php

namespace App\Models\Test;

use App\Models\Model;

class Book extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'cars_books';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'car_id',
        'name',
    ];

    /**
     * Book belongs to Car.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function car()
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}