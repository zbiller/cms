<?php

namespace App\Options;

use App\Models\Model;
use Illuminate\Foundation\Http\FormRequest;

class PreviewOptions
{
    /**
     * The model that should be previewed.
     * When setting this, pass either an instance of App\Models\Model or a string.
     * The "setModel()" method will convert it to a valid model.
     *
     * @var Model|string
     */
    public $model;

    /**
     * The form request validator to validate against.
     *
     * @var FormRequest
     */
    public $validator;

    /**
     * Get a fresh instance of this class.
     *
     * @return PreviewOptions
     */
    public static function instance(): PreviewOptions
    {
        return new static();
    }

    /**
     * Set the $model to work with in the App\Traits\CanPreview trait.
     *
     * @param Model|string $model
     * @return PreviewOptions
     */
    public function setModel($model): PreviewOptions
    {
        $this->model = $model instanceof Model ? $model : app($model);

        return $this;
    }

    /**
     * Set the $validator to work with in the App\Traits\CanPreview trait.
     *
     * @param FormRequest $validator
     * @return PreviewOptions
     */
    public function setValidator(FormRequest $validator): PreviewOptions
    {
        $this->validator = $validator;

        return $this;
    }
}