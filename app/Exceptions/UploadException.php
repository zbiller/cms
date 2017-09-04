<?php

namespace App\Exceptions;

use App\Models\Model;
use Exception;

class UploadException extends Exception
{
    /**
     * @return static
     */
    public static function invalidFile()
    {
        return new static('Invalid file!');
    }

    /**
     * @return static
     */
    public static function fileNotFound()
    {
        return new static('The file does not exist!');
    }

    /**
     * @return static
     */
    public static function originalNotFound()
    {
        return new static('The original upload could not be found!', 404);
    }

    /**
     * @param string|null $type
     * @param int|null $size
     * @return static
     */
    public static function maxSizeExceeded($type = null, $size = null)
    {
        return new static('The uploaded file\'s size exceeds the maximum allowed ' . ($type && $size ? 'for "' . $type . '" files! (' . $size . 'MB)' : '!'));
    }

    /**
     * @param string|null $type
     * @param string|null $extensions
     * @return static
     */
    public static function extensionNotAllowed($type = null, $extensions = null)
    {
        if ($type && $extensions) {
            return new static('The "' . $type . '" extension is not allowed! The extensions allowed are: ' . $extensions);
        } else {
            return new static('The extension is not allowed!');
        }
    }

    /**
     * @param int $width
     * @param int $height
     * @return static
     */
    public static function minimumImageSizeRequired($width, $height)
    {
        return new static('Please choose an image with the minimum size of: ' . $width . 'x' . $height . 'px.');
    }

    /**
     * @return static
     */
    public static function fileUploadFailed()
    {
        return new static('Failed uploading file(s)! Please try again.');
    }

    /**
     * @return static
     */
    public static function fileValidationFailed()
    {
        return new static('Uploader did not pass the validation rules! The uploaded file might be corrupted.');
    }

    /**
     * @return static
     */
    public static function databaseSaveFailed()
    {
        return new static('Failed saving the uploaded file to the database! Please try again.');
    }

    /**
     * @return static
     */
    public static function removeOldUploadsFailed()
    {
        return new static('Failed removing old uploads from disk and/or database! Please try again.');
    }

    /**
     * @return static
     */
    public static function generateImageThumbnailFailed()
    {
        return new static('Thumbnail generation for the uploaded image failed! Please try again.');
    }

    /**
     * @return static
     */
    public static function generateImageStylesFailed()
    {
        return new static('Styles generation for the uploaded image failed! Please try again.');
    }

    /**
     * @return static
     */
    public static function cropImageFailed()
    {
        return new static('Failed cropping the given image! Please try again.');
    }

    /**
     * @return static
     */
    public static function generateVideoThumbnailFailed()
    {
        return new static('Thumbnail generation for the uploaded video failed! Please try again.');
    }

    /**
     * @return static
     */
    public static function generateVideoStylesFailed()
    {
        return new static('Styles generation for the uploaded video failed! Please try again.');
    }

    /**
     * @return static
     */
    public static function invalidUploaderModel()
    {
        return new static(
            'You must specify a loaded or unloaded instance of ' . Model::class . ' for the uploader.' . PHP_EOL .
            'To do this, chain the model() method to the uploader() helper.'
        );
    }

    /**
     * @return static
     */
    public static function invalidUploaderField()
    {
        return new static(
            'You must specify a field for the uploader.' . PHP_EOL .
            'To do this, chain the field() method to the uploader() helper.'
        );
    }
}