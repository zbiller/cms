<?php

namespace App\Models\Shop;

use Swap;
use App\Models\Model;
use App\Traits\IsCacheable;
use Illuminate\Database\Eloquent\Builder;

class Currency extends Model
{
    use IsCacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'currencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabeticallyByName($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Sort the query alphabetically by code.
     *
     * @param Builder $query
     */
    public function scopeAlphabeticallyByCode($query)
    {
        $query->orderBy('code', 'asc');
    }

    /**
     * Filter the query by code.
     *
     * @param Builder $query
     * @param string $code
     */
    public function scopeWhereCode($query, $code)
    {
        $query->where('code', strtoupper($code));
    }

    /**
     * Convert an amount between 2 currencies.
     *
     * @param float $amount
     * @param string $from
     * @param string $to
     * @return float
     */
    public static function convert($amount, $from, $to)
    {
        return $amount * Swap::latest("{$from}/{$to}")->getValue();
    }
}