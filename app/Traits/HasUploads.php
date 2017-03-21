<?php

namespace App\Traits;

use App\Models\Model;
use App\Services\UploadService;
use App\Configs\UploadConfig;
use App\Exceptions\CrudException;
use App\Exceptions\UploadException;
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
                if ($model->isFillable($name) && self::isValidUpload($file)) {
                    try {
                        $config = new UploadConfig($model->getUploadConfig());
                        $upload = new UploadService($name, $file, $model, $config);

                        $model->attributes[$name] = $upload->upload();
                    } catch (UploadException $e) {
                        throw new CrudException($e->getMessage());
                    }
                }
            }
        });
    }

    /**
     * Verify if the file provided for uploaded is a valid one.
     *
     * @param UploadedFile $file
     * @return bool
     */
    protected static function isValidUpload(UploadedFile $file)
    {
        return $file instanceof UploadedFile && $file->getPath() != '';
    }
}