<?php

namespace App\Models\Test;

use App\Models\Model;

class Brand extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'cars_brands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Brand has many Cars.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cars()
    {
        return $this->hasMany(Car::class, 'brand_id');
    }
}