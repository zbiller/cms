<?php

namespace App\Exceptions;

use Exception;

class CurrencyException extends Exception
{
    /**
     * @param string $currency
     * @param string|null $default
     * @return static
     */
    public static function invalidCurrency($currency, $default = null)
    {
        if (!$default) {
            $default = config('shop.price.default_currency');
        }

        return new static("{$currency}/{$default} currency exchange rate update failed. The {$currency} is obsolete.", 422);
    }
}