<?php

namespace App\Exceptions;

use Exception;

class VerificationException extends Exception
{
    /**
     * @return static
     */
    public static function invalidEmail()
    {
        return new static('Could not generate an email verification token, because the user does not have any email!');
    }

    /**
     * @return static
     */
    public static function invalidToken()
    {
        return new static('Invalid verification token supplied!');
    }
}