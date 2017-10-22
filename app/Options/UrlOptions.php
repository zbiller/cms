<?php

namespace App\Options;

class UrlOptions
{
    /**
     * The controller where the laravel router should dispatch the request.
     * This is used when a URI is accessed by a user.
     * The format of this property should be Full\Namespace\Of\Controller
     *
     * @var string
     */
    public $routeController;

    /**
     * The controller where the laravel router should dispatch the request.
     * This is used when a URI is accessed by a user.
     * The format of this property should be simply the name of the method residing inside the $routeController
     *
     * @var string
     */
    public $routeAction;

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
     * The symbol that will be used to glue url segments together.
     *
     * @var string
     */
    public $urlGlue = '/';

    /**
     * Flag whether to update children urls on parent url save.
     *
     * @var bool
     */
    public $cascadeUpdate = true;

    /**
     * Get a fresh instance of this class.
     *
     * @return UrlOptions
     */
    public static function instance(): UrlOptions
    {
        return new static();
    }

    /**
     * Set the $urlRoute to work with in the App\Traits\HasUrl trait.
     *
     * @param string $controller
     * @param string $action
     * @return UrlOptions
     */
    public function routeUrlTo($controller, $action): UrlOptions
    {
        $this->routeController = $controller;
        $this->routeAction = $action;

        return $this;
    }

    /**
     * Set the $fromField to work with in the App\Traits\HasUrl trait.
     *
     * @param string|array|callable $field
     * @return UrlOptions
     */
    public function generateUrlSlugFrom($field): UrlOptions
    {
        $this->fromField = $field;

        return $this;
    }

    /**
     * Set the $toField to work with in the App\Traits\HasUrl trait.
     *
     * @param string $field
     * @return UrlOptions
     */
    public function saveUrlSlugTo($field): UrlOptions
    {
        $this->toField = $field;

        return $this;
    }

    /**
     * Set the $urlPrefix to work with in the App\Traits\HasUrl trait.
     *
     * @param string|array|callable $prefix
     * @return UrlOptions
     */
    public function prefixUrlWith($prefix): UrlOptions
    {
        $this->urlPrefix = $prefix;

        return $this;
    }

    /**
     * Set the $urlSuffix to work with in the App\Traits\HasUrl trait.
     *
     * @param string|array|callable $suffix
     * @return UrlOptions
     */
    public function suffixUrlWith($suffix): UrlOptions
    {
        $this->urlSuffix = $suffix;

        return $this;
    }

    /**
     * Set the $urlGlue to work with in the App\Traits\HasUrl trait.
     *
     * @param string|array|callable $glue
     * @return UrlOptions
     */
    public function glueUrlWith($glue): UrlOptions
    {
        $this->urlGlue = $glue;

        return $this;
    }

    /**
     * Set the $cascadeUpdate to work with in the App\Traits\HasUrl trait.
     *
     * @return UrlOptions
     */
    public function doNotUpdateCascading(): UrlOptions
    {
        $this->cascadeUpdate = false;

        return $this;
    }
}