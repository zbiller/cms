<?php

namespace App\Exceptions;

use App\Models\Shop\Product;
use Exception;

class CartException extends Exception
{
    /**
     * @return static
     */
    public static function productAddFailed()
    {
        return new static('Failed adding the product to cart!');
    }

    /**
     * @return static
     */
    public static function productUpdateFailed()
    {
        return new static('Failed updating the product from cart!');
    }

    /**
     * @param bool $multiple
     * @return static
     */
    public static function productRemoveFailed($multiple = false)
    {
        return new static('Failed removing the product' . ($multiple ? 's' : '') . ' from cart!');
    }

    /**
     * @return static
     */
    public static function invalidProduct()
    {
        return new static('Invalid product! The product must be an existing ' . Product::class . '.');
    }

    /**
     * @return static
     */
    public static function quantityExceeded()
    {
        return new static('The quantity specified exceeds the product\'s quantity!');
    }

    /**
     * @return static
     */
    public static function cleanupFailed()
    {
        return new static('Failed cleaning up the old carts! Please try again.');
    }
}