<?php

namespace App\Exceptions;

use Exception;

class RevisionException extends Exception
{
    /**
     * @return static
     */
    public static function saveFailed()
    {
        return new static('Failed saving the revision for the record!');
    }

    /**
     * @return static
     */
    public static function rollbackFailed()
    {
        return new static('Failed rolling back the record to the specified revision!');
    }

    /**
     * @param bool $multiple
     * @return static
     */
    public static function deleteFailed($multiple = false)
    {
        return new static('Failed deleting the record\'s revision' . ($multiple ? 's' : '') . '!');
    }
}