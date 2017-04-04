<?php

namespace App\Traits;

use Storage;
use App\Services\UploadService;
use App\Helpers\UploadedHelper;
use App\Exceptions\UploadException;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

trait HasUploads
{
    /**
     * This method should be called inside the models using this trait.
     * In this method you can overwrite any config value set in config/upload.php
     * Just return an array like in config/upload.php, specifying only the keys you wish to overwrite.
     *
     * @return array
     */
    abstract function getUploadConfig(): array;

    /**
     * When creating/updating a record.
     * Get all uploaded files from request and try to upload them.
     * Once upload is done, initialize the model's field name with the uploaded file's name, for storing to database.
     * If upload fails to execute throw an exception which is caught in the CanCrud trait, meaning that the model won't be saved either.
     *
     * @return void
     */
    public static function bootHasUploads()
    {
        static::saving(function (Model $model) {
            foreach (request()->allFiles() as $name => $file) {
                if (self::isValidFile($model, $file, $name)) {
                    try {
                        $upload = (new UploadService($file, $model, $name))->upload();

                        $model->attributes[$name] = $upload->getPath() . '/' . $upload->getName();
                    } catch (UploadException $e) {
                        throw new CrudException($e->getMessage(), $e->getCode(), $e);
                    }
                }
            }
        });
    }

    /**
     * Get the upload attribute as an upload helper instance.
     * To achieve this, call the respective upload field property prefixed with an underscore "_".
     * The script will look to see if it's meant for an upload and it will return the helper.
     * The returned helper instance will contain the upload file coming from database field, without the underscore "_".
     * Afterwards, you can call App\Helpers\UploadedHelper methods directly on the attribute.
     *
     * @param string $key
     * @return UploadedHelper|mixed
     */
    public function getAttribute($key)
    {
        if (starts_with($key, '_')) {
            if (Storage::disk(config('upload.storage.disk'))->exists($this->{ltrim($key, '_')})) {
                return new UploadedHelper($this->{ltrim($key, '_')});
            }
        }

        return parent::getAttribute($key);
    }

    /**
     * @param Model $model
     * @param UploadedFile $file
     * @param string $name
     * @return bool
     */
    private static function isValidFile(Model $model, UploadedFile $file, $name)
    {
        return $model->isFillable($name) && request()->file($name)->isValid() && $file instanceof UploadedFile;
    }
}