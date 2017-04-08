<?php

namespace App\Options;

class HasUrlOptions
{
    /**
     * The field used to generate the url slug from.
     *
     * @var string
     */
    public $fromField;

    /**
     * The field where to store the generated url slug.
     *
     * @var string
     */
    public $toField;

    /**
     * The prefix that should be prepended to the generated url slug.
     *
     * @var string
     */
    public $urlPrefix;

    /**
     * The suffix that should be appended to the generated url slug.
     *
     * @var string
     */
    public $urlSuffix;

    /**
     * Flag whether to update children urls on parent url save.
     *
     * @var bool
     */
    public $cascadeUpdate = true;

    /**
     * Get a fresh instance of this class.
     *
     * @return HasUrlOptions
     */
    public static function instance(): HasUrlOptions
    {
        return new static();
    }

    /**
     * Set the $fromField to work with in the App\Traits\HasUrl trait.
     *
     * @param string|array|callable $field
     * @return HasUrlOptions
     */
    public function generateUrlSlugFrom($field): HasUrlOptions
    {
        $this->fromField = $field;

        return $this;
    }

    /**
     * Set the $toField to work with in the App\Traits\HasUrl trait.
     *
     * @param string $field
     * @return HasUrlOptions
     */
    public function saveUrlSlugTo($field): HasUrlOptions
    {
        $this->toField = $field;

        return $this;
    }

    /**
     * Set the $urlPrefix to work with in the App\Traits\HasUrl trait.
     *
     * @param string|array|callable $prefix
     * @return HasUrlOptions
     */
    public function prefixUrlWith($prefix): HasUrlOptions
    {
        $this->urlPrefix = $prefix;

        return $this;
    }

    /**
     * Set the $urlSuffix to work with in the App\Traits\HasUrl trait.
     *
     * @param string|array|callable $suffix
     * @return HasUrlOptions
     */
    public function suffixUrlWith($suffix): HasUrlOptions
    {
        $this->urlSuffix = $suffix;

        return $this;
    }

    /**
     * Set the $cascadeUpdate to work with in the App\Traits\HasUrl trait.
     *
     * @return HasUrlOptions
     */
    public function doNotUpdateCascading(): HasUrlOptions
    {
        $this->cascadeUpdate = false;

        return $this;
    }
}