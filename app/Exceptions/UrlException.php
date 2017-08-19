<?php

namespace App\Exceptions;

use Exception;

class UrlException extends Exception
{
    /**
     * @return static
     */
    public static function createFailed()
    {
        return new static('Failed creating the url!');
    }

    /**
     * @return static
     */
    public static function updateFailed()
    {
        return new static('Failed updating the url!');
    }

    /**
     * @return static
     */
    public static function deleteFailed()
    {
        return new static('Failed deleting the url!');
    }
}