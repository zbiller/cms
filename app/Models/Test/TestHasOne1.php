<?php

namespace App\Models\Test;

use App\Models\Model;

class TestHasOne1 extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'test_hasone_1';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'test_id',
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }
}