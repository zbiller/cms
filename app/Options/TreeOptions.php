<?php

namespace App\Options;

use App\Models\Model;
use App\Http\Filters\Filter;
use App\Http\Sorts\Sort;

class TreeOptions
{
    /**
     * The model used to apply tree logic on.
     *
     * @var Model
     */
    public $model;

    /**
     * The filter type used to filter items before rendering in tree.
     *
     * @var Filter
     */
    public $filter;

    /**
     * The sort type used to sort items before rendering in tree.
     *
     * @var Sort
     */
    public $sort;

    /**
     * The name of the tree's root node.
     *
     * @var string
     */
    public $name;

    /**
     * The view to be rendered on listing tree items.
     *
     * @var string
     */
    public $view;

    /**
     * The variables to be appended tp the view rendered on listing tree items.
     *
     * @var array
     */
    public $vars = [];

    /**
     * The slug property on the model to be used in url re-building on tree.
     *
     * @var string
     */
    public $slug = 'slug';

    /**
     * The model's property used to display the node text.
     *
     * @var string
     */
    public $text = 'name';


    /**
     * The model's property used to get the children collection.
     *
     * @var string
     */
    public $children = 'children';

    /**
     * Get a fresh instance of this class.
     *
     * @return TreeOptions
     */
    public static function instance(): TreeOptions
    {
        return new static();
    }

    /**
     * Set the $model to work with in the App\Traits\CanHandleTree trait.
     *
     * @param Model $model
     * @return TreeOptions
     */
    public function setModel($model): TreeOptions
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set the $filter to work with in the App\Traits\CanHandleTree trait.
     *
     * @param Filter $filter
     * @return TreeOptions
     */
    public function setFilter($filter): TreeOptions
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Set the $sort to work with in the App\Traits\CanHandleTree trait.
     *
     * @param Sort $sort
     * @return TreeOptions
     */
    public function setSort($sort): TreeOptions
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Set the $name to work with in the App\Traits\CanHandleTree trait.
     *
     * @param string $name
     * @return TreeOptions
     */
    public function setName($name): TreeOptions
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the $view to work with in the App\Traits\CanHandleTree trait.
     *
     * @param string $view
     * @return TreeOptions
     */
    public function setView($view): TreeOptions
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Set the $vars to work with in the App\Traits\CanHandleTree trait.
     *
     * @param array $vars
     * @return TreeOptions
     */
    public function setVars(array $vars = []): TreeOptions
    {
        $this->vars = $vars;

        return $this;
    }

    /**
     * Set the $slug to work with in the App\Traits\CanHandleTree trait.
     *
     * @param string $slug
     * @return TreeOptions
     */
    public function setSlug($slug): TreeOptions
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Set the $name to work with in the App\Traits\CanHandleTree trait.
     *
     * @param string $text
     * @return TreeOptions
     */
    public function setText($text): TreeOptions
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Set the $name to work with in the App\Traits\CanHandleTree trait.
     *
     * @param string $children
     * @return TreeOptions
     */
    public function setChildren($children): TreeOptions
    {
        $this->children = $children;

        return $this;
    }
}