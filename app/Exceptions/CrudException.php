<?php

namespace App\Exceptions;

use Exception;

class CrudException extends Exception
{
    /**
     * @return static
     */
    public static function deletionRestrictedDueToChildren()
    {
        return new static('Could not delete the record because it has children!');
    }
}