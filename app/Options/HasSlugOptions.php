<?php

namespace App\Options;

class HasSlugOptions
{
    /**
     * The field used to generate the slug from.
     *
     * @var string
     */
    public $fromField;

    /**
     * The field where to store the generated slug.
     *
     * @var string
     */
    public $toField;

    /**
     * Flag whether slugs should be unique or not.
     *
     * @var bool
     */
    public $uniqueSlugs = true;

    /**
     * Flag whether to generate slug on model create event or not.
     *
     * @var bool
     */
    public $generateSlugOnCreate = true;

    /**
     * Flag whether to generate slug on model update event or not.
     *
     * @var bool
     */
    public $generateSlugOnUpdate = true;

    /**
     * Get a fresh instance of this class.
     *
     * @return HasSlugOptions
     */
    public static function instance(): HasSlugOptions
    {
        return new static();
    }

    /**
     * Set the $fromField to work with in the App\Traits\HasSlug trait.
     *
     * @param string|array|callable $field
     * @return HasSlugOptions
     */
    public function generateSlugFrom($field): HasSlugOptions
    {
        $this->fromField = $field;

        return $this;
    }

    /**
     * Set the $toField to work with in the App\Traits\HasSlug trait.
     *
     * @param string $field
     * @return HasSlugOptions
     */
    public function saveSlugTo($field): HasSlugOptions
    {
        $this->toField = $field;

        return $this;
    }

    /**
     * Set the $uniqueSlugs to work with in the App\Traits\HasSlug trait.
     *
     * @return HasSlugOptions
     */
    public function allowDuplicateSlugs(): HasSlugOptions
    {
        $this->uniqueSlugs = false;

        return $this;
    }

    /**
     * Set the $generateSlugOnCreate to work with in the App\Traits\HasSlug trait.
     *
     * @return HasSlugOptions
     */
    public function doNotGenerateSlugOnCreate(): HasSlugOptions
    {
        $this->generateSlugOnCreate = false;

        return $this;
    }

    /**
     * Set the $generateSlugOnUpdate to work with in the App\Traits\HasSlug trait.
     *
     * @return HasSlugOptions
     */
    public function doNotGenerateSlugOnUpdate(): HasSlugOptions
    {
        $this->generateSlugOnUpdate = false;

        return $this;
    }
}