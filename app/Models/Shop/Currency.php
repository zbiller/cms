<?php

namespace App\Models\Shop;

use App\Exceptions\CurrencyException;
use App\Models\Model;
use App\Options\ActivityOptions;
use App\Traits\HasActivity;
use App\Traits\IsCacheable;
use App\Traits\IsFilterable;
use App\Traits\IsSortable;
use Exception;
use Exchanger\Exception\ChainException;
use Illuminate\Database\Eloquent\Builder;
use Swap;

class Currency extends Model
{
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

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
        'symbol',
        'format',
        'exchange_rate',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'exchange_rate' => 'float'
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
     * @param mixed $value
     */
    public function setExchangeRateAttribute($value)
    {
        $this->attributes['exchange_rate'] = $value ?: 0.0000;
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

    /**
     * Update the exchange rates for one or all currencies using the Swap package.
     *
     * @param Currency|null $currency
     * @return bool
     */
    public static function updateExchangeRates(Currency $currency = null)
    {
        set_time_limit(0);

        $default = config('shop.price.default_currency');
        $currencies = $currency && $currency->exists ? collect()->push($currency) : static::all();

        $currencies->each(function ($item) use ($default) {
            try {
                $item->exchange_rate = Swap::latest("{$item->code}/{$default}")->getValue();
                $item->save();
            } catch (ChainException $e) {
                throw new CurrencyException(
                    "{$item->code}/{$default} currency exchange rate update failed. The {$item->code} is obsolete.", 422, $e
                );
            } catch (Exception $e) {
                throw new CurrencyException(
                    $e->getMessage(), $e->getCode(), $e
                );
            }
        });

        return true;
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public static function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->logByField('name');
    }
}