<?php

namespace App\Options;

use App\Models\Model;
use Exception;

class DuplicateOptions
{
    /**
     * The database columns from the model that should be excluded (ignored) when duplicating the record.
     *
     * IMPORTANT: This option is available for the App\Traits\HasDuplicates trait.
     *
     * @var array
     */
    private $excludedColumns;

    /**
     * The database columns from the model that should be unique when duplicating the record.
     *
     * IMPORTANT: This option is available for the App\Traits\HasDuplicates trait.
     *
     * @var array
     */
    private $uniqueColumns;

    /**
     * The database relations of the model that should be excluded (ignored) when duplicating the record.
     *
     * IMPORTANT: This option is available for the App\Traits\HasDuplicates trait.
     *
     * @var array
     */
    private $excludedRelations;

    /**
     * The database columns for each model's relation tha should be excluded (ignored) when duplicating the record.
     *
     * IMPORTANT: This option is available for the App\Traits\HasDuplicates trait.
     *
     * @var array
     */
    private $excludedRelationColumns;

    /**
     * The database columns for each model's relation that should be unique when duplicating the record.
     *
     * IMPORTANT: This option is available for the App\Traits\HasDuplicates trait.
     *
     * @var array
     */
    private $uniqueRelationColumns;

    /**
     * Flag indicating if when duplicating a record, the script should also duplicate it's relations.
     *
     * IMPORTANT: This option is available for the App\Traits\HasDuplicates trait.
     *
     * @var bool
     */
    private $shouldDuplicateDeeply = true;

    /**
     * The model that should be duplicated.
     * When setting this, pass either an instance of App\Models\Model or a string.
     * The "setEntityModel()" method will convert it to a valid model.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDuplicate trait.
     *
     * @var Model
     */
    private $entityModel;

    /**
     * The url to redirect to after an entity has been duplicated.
     * When setting this, pass a string for the entity's "edit" route.
     * The CanDuplicate trait will automatically transform that into a redirect applying the duplicated id at the end.
     *
     * IMPORTANT: This option is available for the App\Traits\CanDuplicate trait.
     *
     * @var string
     */
    private $redirectUrl;

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
     * @return DuplicateOptions
     */
    public static function instance(): DuplicateOptions
    {
        return new static();
    }

    /**
     * Set the $excludedColumns to work with in the App\Traits\HasDuplicates trait.
     *
     * @param $columns
     * @return DuplicateOptions
     */
    public function excludeColumns(...$columns): DuplicateOptions
    {
        $this->excludedColumns = array_flatten($columns);

        return $this;
    }

    /**
     * Set the $uniqueColumns to work with in the App\Traits\HasDuplicates trait.
     *
     * @param $columns
     * @return DuplicateOptions
     */
    public function uniqueColumns(...$columns): DuplicateOptions
    {
        $this->uniqueColumns = array_flatten($columns);

        return $this;
    }

    /**
     * Set the $excludedRelations to work with in the App\Traits\HasDuplicates trait.
     *
     * @param $relations
     * @return DuplicateOptions
     */
    public function excludeRelations(...$relations): DuplicateOptions
    {
        $this->excludedRelations = array_flatten($relations);

        return $this;
    }

    /**
     * Set the $excludedRelationColumns to work with in the App\Traits\HasDuplicates trait.
     *
     * Param $relations:
     * --- associative array with keys containing each relation name and values (array) containing the excluded columns for each relation.
     *
     * @param array $columns
     * @return DuplicateOptions
     */
    public function excludeRelationColumns(array $columns = []): DuplicateOptions
    {
        $this->excludedRelationColumns = $columns;

        return $this;
    }

    /**
     * Set the $uniqueRelationColumns to work with in the App\Traits\HasDuplicates trait.
     *
     * Param $relations:
     * --- associative array with keys containing each relation name and values (array) containing the unique columns for each relation.
     *
     * @param array $columns
     * @return DuplicateOptions
     */
    public function uniqueRelationColumns(array $columns = []): DuplicateOptions
    {
        $this->uniqueRelationColumns = $columns;

        return $this;
    }

    /**
     * Set the $shouldDuplicateDeeply to work with in the App\Traits\HasDuplicates trait.
     *
     * @return DuplicateOptions
     */
    public function disableDeepDuplication(): DuplicateOptions
    {
        $this->shouldDuplicateDeeply = false;

        return $this;
    }

    /**
     * Set the $entityModel to work with in the App\Traits\CanDuplicate trait.
     *
     * @param Model|string $model
     * @return DuplicateOptions
     */
    public function setEntityModel($model): DuplicateOptions
    {
        $this->entityModel = $model instanceof Model ? $model : app($model);

        return $this;
    }

    /**
     * Set the $redirectUrl to work with in the App\Traits\CanDuplicate trait.
     *
     * @param $redirect
     * @return DuplicateOptions
     */
    public function setRedirectUrl($redirect): DuplicateOptions
    {
        $this->redirectUrl = $redirect;

        return $this;
    }
}