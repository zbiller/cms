<?php

namespace App\Traits;

use App\Exceptions\SlugException;
use App\Models\Model;
use App\Options\HasSlugOptions;

trait HasSlug
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\HasSlugOptions file.
     *
     * @var HasSlugOptions
     */
    protected $hasSlugOptions;

    /**
     * The method used for setting the slug options.
     * This method should be called inside the model using this trait.
     * Inside the method, you should set all the slug options.
     * This can be achieved using the methods from App\Options\HasSlugOptions.
     *
     * @return HasSlugOptions
     */
    abstract function getHasSlugOptions(): HasSlugOptions;

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasSlug()
    {
        static::creating(function (Model $model) {
            $model->generateSlugOnCreate();
        });

        static::updating(function (Model $model) {
            $model->generateSlugOnUpdate();
        });
    }

    /**
     * The logic for actually setting the slug.
     *
     * @return void
     */
    public function generateSlug()
    {
        $this->checkSlugOptions();

        if ($this->slugHasBeenSupplied()) {
            $slug = $this->generateNonUniqueSlug();

            if ($this->hasSlugOptions->uniqueSlugs) {
                $slug = $this->makeSlugUnique($slug);
            }

            $this->setAttribute($this->hasSlugOptions->toField, $slug);
        }
    }

    /**
     * Handle setting the slug on model creation.
     *
     * @return void
     */
    protected function generateSlugOnCreate()
    {
        $this->initSlugOptions();

        if ($this->hasSlugOptions->generateSlugOnCreate === false) {
            return;
        }

        $this->generateSlug();
    }

    /**
     * Handle setting the slug on model update.
     *
     * @return void
     */
    protected function generateSlugOnUpdate()
    {
        $this->initSlugOptions();

        if ($this->hasSlugOptions->generateSlugOnUpdate === false) {
            return;
        }

        $this->generateSlug();
    }

    /**
     * Generate a non unique slug for this record.
     *
     * @return string
     */
    protected function generateNonUniqueSlug()
    {
        if ($this->slugHasChanged()) {
            return str_slug($this->getAttribute($this->hasSlugOptions->toField));
        }

        return str_slug($this->getSlugSource());
    }

    /**
     * Make the given slug unique.
     * 
     * @param string $slug
     * @return string
     */
    protected function makeSlugUnique($slug)
    {
        $original = $slug;
        $i = 1;

        while ($this->slugAlreadyExists($slug) || $slug === '') {
            $slug = $original . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Check if the $fromField slug has been supplied.
     * If not, then skip the entire slug generation.
     *
     * @return bool
     */
    protected function slugHasBeenSupplied()
    {
        return $this->getAttribute($this->hasSlugOptions->fromField) !== null;
    }

    /**
     * Determine if a custom slug has been saved.
     *
     * @return bool
     */
    protected function slugHasChanged()
    {
        return
            $this->getOriginal($this->hasSlugOptions->toField) &&
            $this->getOriginal($this->hasSlugOptions->toField) != $this->getAttribute($this->hasSlugOptions->toField);
    }

    /**
     * Get the string that should be used as base for the slug.
     *
     * @return string
     */
    protected function getSlugSource()
    {
        if (is_callable($this->hasSlugOptions->fromField)) {
            $source = call_user_func($this->hasSlugOptions->fromField, $this);

            return substr($source, 0, $this->hasSlugOptions->fromField);
        }

        return collect($this->hasSlugOptions->fromField)->map(function ($field) {
            return $this->getAttribute($field) ?: '';
        })->implode('-');
    }

    /**
     * Check if the given slug already exists on another record.
     *
     * @param string $slug
     * @return bool
     */
    protected function slugAlreadyExists($slug)
    {
        return (bool)static::where($this->hasSlugOptions->toField, $slug)
            ->where($this->getKeyName(), '!=', $this->getKey() ?: '0')
            ->first();
    }

    /**
     * Set the slug options.
     *
     * @return $this
     */
    protected function initSlugOptions()
    {
        $this->hasSlugOptions = $this->getHasSlugOptions();

        return $this;
    }

    /**
     * Check if mandatory slug options have been properly set from the model.
     * Check if $fromField and $toField have been set.
     *
     * @return void
     * @throws SlugException
     */
    protected function checkSlugOptions()
    {
        if (!count($this->hasSlugOptions->fromField)) {
            throw new SlugException(
                'The model ' . get_class($this) . ' uses the HasSlug trait' . PHP_EOL .
                'You are required to set the field from where to generate the slug ($fromField)' . PHP_EOL .
                'You can do this from inside the getHasSlugOptions() method defined on the model.'
            );
        }

        if (!strlen($this->hasSlugOptions->toField)) {
            throw new SlugException(
                'The model ' . get_class($this) . ' uses the HasSlug trait' . PHP_EOL .
                'You are required to set the field where to store the generated slug ($toField)' . PHP_EOL .
                'You can do this from inside the getHasSlugOptions() method defined on the model.'
            );
        }
    }
}