<?php

namespace App\Models\Shop;

use App\Models\Model;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;

class Cart extends Model
{
    use HasActivity;
    use IsCacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'cart';

    /**
     * The attributes that mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'set_id',
        'name',
        'slug',
        'value',
        'type',
    ];
}