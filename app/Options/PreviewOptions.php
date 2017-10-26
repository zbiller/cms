<?php

namespace App\Options;

use App\Models\Model;
use Exception;
use Illuminate\Foundation\Http\FormRequest;

class PreviewOptions
{
    /**
     * The model that should be previewed.
     * When setting this, pass either an instance of App\Models\Model or a string.
     * The "setEntityModel()" method will convert it to a valid model.
     *
     * @var Model|string
     */
    private $entityModel;

    /**
     * The form request validator to validate against.
     *
     * @var FormRequest
     */
    private $validatorRequest;

    /**
     * The pivoted relations that should be saved alongside the original model when previewing.
     *
     * The $pivotedRelations parameter should be an associative array where the:
     * - keys: represent each pivoted relation's name defined on the model.
     * - values: represent the request array key name responsible for passing data into the "attach" or "sync" methods.
     *
     * @var array
     */
    private $pivotedRelations = [];

    /**
     * Get the value of a property of this class.
     *
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (property_exists(static::class, $name)) {
            return $this->{$name};
        }

        throw new Exception(
            'The property "' . $name . '" does not exist in class "' . static::class . '"'
        );
    }

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
     * Set the $entityModel to work with in the App\Traits\CanPreview trait.
     *
     * @param Model|string $model
     * @return PreviewOptions
     */
    public function setEntityModel($model): PreviewOptions
    {
        $this->entityModel = $model instanceof Model ? $model : app($model);

        return $this;
    }

    /**
     * Set the $validatorRequest to work with in the App\Traits\CanPreview trait.
     *
     * @param FormRequest $validator
     * @return PreviewOptions
     */
    public function setValidatorRequest(FormRequest $validator): PreviewOptions
    {
        $this->validatorRequest = $validator;

        return $this;
    }

    /**
     * Set the $pivotedRelations to work with in the App\Traits\CanPreview trait.
     *
     * @param array $relations
     * @return PreviewOptions
     */
    public function withPivotedRelations(array $relations = []): PreviewOptions
    {
        $this->pivotedRelations = $relations;

        return $this;
    }
}