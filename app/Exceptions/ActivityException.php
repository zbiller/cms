<?php

namespace App\Exceptions;

use Exception;

class ActivityException extends Exception
{
    /**
     * @return static
     */
    public static function cleanupFailed()
    {
        return new static('Could not clean up the activity! Please try again.');
    }
}