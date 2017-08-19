<?php

namespace App\Exceptions;

use Exception;

class DraftException extends Exception
{
    /**
     * @return static
     */
    public static function saveFailed()
    {
        return new static('Failed saving the draft for the record!');
    }

    /**
     * @return static
     */
    public static function publishFailed()
    {
        return new static('Failed publishing the record!');
    }

    /**
     * @param bool $multiple
     * @return static
     */
    public static function deleteFailed($multiple = false)
    {
        return new static('Failed deleting the draft' . ($multiple ? 's' : '') . '!');
    }
}