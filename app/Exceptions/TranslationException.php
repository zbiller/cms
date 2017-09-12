<?php

namespace App\Exceptions;

use Exception;

class TranslationException extends Exception
{
    /**
     * @param string $key
     * @return static
     */
    public static function attributeIsNotTranslatable($key)
    {
        return new static('Attribute "' . $key . '" is not translatable!');
    }
}