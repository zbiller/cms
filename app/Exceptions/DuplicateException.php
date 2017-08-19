<?php

namespace App\Exceptions;

use Exception;

class DuplicateException extends Exception
{
    /**
     * @return static
     */
    public static function duplicateFailed()
    {
        return new static('Failed duplicating the record!');
    }
}