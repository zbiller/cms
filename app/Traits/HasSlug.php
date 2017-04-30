<?php

namespace App\Traits;

use Exception;
use ReflectionMethod;
use App\Models\Model;
use App\Options\SlugOptions;
use App\Exceptions\SlugException;

trait HasSlug
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\SlugOptions file.
     *
     * @var SlugOptions
     */
    protected static $slugOptions;

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasSlug()
    {
        self::checkSlugOptions();

        self::$slugOptions = self::getSlugOptions();

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
        self::validateSlugOptions();

        if ($this->slugHasBeenSupplied()) {
            $slug = $this->generateNonUniqueSlug();

            if (self::$slugOptions->uniqueSlugs) {
                $slug = $this->makeSlugUnique($slug);
            }

            $this->setAttribute(self::$slugOptions->toField, $slug);
        }
    }

    /**
     * Handle setting the slug on model creation.
     *
     * @return void
     */
    protected function generateSlugOnCreate()
    {
        if (self::$slugOptions->generateSlugOnCreate === false) {
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
        if (self::$slugOptions->generateSlugOnUpdate === false) {
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
            $source = $this->getAttribute(self::$slugOptions->toField);

            return str_is('/', $source) ? $source : str_slug($source);
        }

        $source = $this->getSlugSource();

        return str_is('/', $source) ? $source : str_slug($source);
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
        return $this->getAttribute(self::$slugOptions->fromField) !== null;
    }

    /**
     * Determine if a custom slug has been saved.
     *
     * @return bool
     */
    protected function slugHasChanged()
    {
        return
            $this->getOriginal(self::$slugOptions->toField) &&
            $this->getOriginal(self::$slugOptions->toField) != $this->getAttribute(self::$slugOptions->toField);
    }

    /**
     * Get the string that should be used as base for the slug.
     *
     * @return string
     */
    protected function getSlugSource()
    {
        if (is_callable(self::$slugOptions->fromField)) {
            $source = call_user_func(self::$slugOptions->fromField, $this);

            return substr($source, 0, self::$slugOptions->fromField);
        }

        return collect(self::$slugOptions->fromField)->map(function ($field) {
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
        return (bool)static::where(self::$slugOptions->toField, $slug)
            ->where($this->getKeyName(), '!=', $this->getKey() ?: '0')
            ->first();
    }

    /**
     * Check if mandatory slug options have been properly set from the model.
     * Check if $fromField and $toField have been set.
     *
     * @return void
     * @throws SlugException
     */
    protected static function validateSlugOptions()
    {
        if (!count(self::$slugOptions->fromField)) {
            throw new SlugException(
                'The model ' . self::class . ' uses the HasSlug trait' . PHP_EOL .
                'You are required to set the field from where to generate the slug ($fromField)' . PHP_EOL .
                'You can do this from inside the getSlugOptions() method defined on the model.'
            );
        }

        if (!strlen(self::$slugOptions->toField)) {
            throw new SlugException(
                'The model ' . self::class . ' uses the HasSlug trait' . PHP_EOL .
                'You are required to set the field where to store the generated slug ($toField)' . PHP_EOL .
                'You can do this from inside the getSlugOptions() method defined on the model.'
            );
        }
    }

    /**
     * Verify if the getSlugOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkSlugOptions()
    {
        if (!method_exists(self::class, 'getSlugOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getSlugOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getSlugOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getSlugOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}