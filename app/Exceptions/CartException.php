<?php

namespace App\Exceptions;

use Exception;

class CartException extends Exception
{
    /**
     * @return static
     */
    public static function invalidProductInstance()
    {
        return new static('Invalid Product! The product instance must represent an existing model.');
    }

    /**
     * @return static
     */
    public static function quantityExceeded()
    {
        return new static('The quantity specified exceeds the product\'s quantity!');
    }
}