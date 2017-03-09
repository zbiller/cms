<?php

namespace App\Models\Test;

use App\Models\Model;
use App\Traits\CanFilter;
use App\Traits\CanSort;

class Test extends Model
{
    use CanFilter, CanSort;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'test';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
    ];
}