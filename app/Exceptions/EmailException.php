<?php

namespace App\Exceptions;

use Exception;

class EmailException extends Exception
{
    /**
     * @param string $identifier
     * @return static
     */
    public static function emailNotFound($identifier)
    {
        return new static('No email with the "' . $identifier . '" identifier was found!', 404);
    }

    /**
     * @return static
     */
    public static function viewNotFound()
    {
        return new static('Email view not found!', 404);
    }
}