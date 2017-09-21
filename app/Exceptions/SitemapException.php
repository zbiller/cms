<?php

namespace App\Exceptions;

use Exception;

class SitemapException extends Exception
{
    /**
     * @return static
     */
    public static function xmlFileNotFound()
    {
        return new static('The sitemap xml file does not exist!');
    }

    /**
     * @return static
     */
    public static function xmlGenerationFailed()
    {
        return new static('Failed generating the sitemap xml file!');
    }
}