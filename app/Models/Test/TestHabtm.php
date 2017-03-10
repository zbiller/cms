<?php

namespace App\Models\Test;

use App\Models\Model;

class TestHabtm extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'test_habtm';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tests()
    {
        return $this->belongsToMany(Test::class, 'test_test_habtm_ring', 'test_habtm_id', 'test_id');
    }
}