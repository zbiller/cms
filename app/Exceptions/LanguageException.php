<?php

namespace App\Exceptions;

use Exception;

class LanguageException extends Exception
{
    /**
     * @return static
     */
    public static function oneDefaultIsRequired()
    {
        return new static('A default language is required at all times!');
    }

    /**
     * @return static
     */
    public static function replacingTheDefaultHasFailed()
    {
        return new static('Could not replace the default language!');
    }

    /**
     * @return static
     */
    public static function deletingTheDefaultIsRestricted()
    {
        return new static('Deleting the default language is restricted!');
    }
}