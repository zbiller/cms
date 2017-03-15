<?php

namespace App\Options;

use App\Models\Model;

class CanCrudOptions
{
    /**
     * The instantiated model to work with.
     *
     * @var Model
     */
    public $model;

    /**
     * The list route and view as strings.
     *
     * @var
     */
    public $listRoute;
    public $listView;

    /**
     * The add route and view as strings.
     *
     * @var
     */
    public $addRoute;
    public $addView;

    /**
     * The edit route and view as strings.
     *
     * @var
     */
    public $editRoute;
    public $editView;

    /**
     * Get a fresh instance of this class.
     *
     * @return CanCrudOptions
     */
    public static function instance(): CanCrudOptions
    {
        return new static();
    }

    /**
     * Set the model to work with in the App\Traits\CanCrud trait.
     *
     * @param Model $model
     * @return CanCrudOptions
     */
    public function setModel(Model $model): CanCrudOptions
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set the list route to work with in the App\Traits\CanCrud trait.
     *
     * @param string $route
     * @return CanCrudOptions
     */
    public function setListRoute($route): CanCrudOptions
    {
        $this->listRoute = $route;

        return $this;
    }

    /**
     * Set the list view to work with in the App\Traits\CanCrud trait.
     *
     * @param string $view
     * @return CanCrudOptions
     */
    public function setListView($view): CanCrudOptions
    {
        $this->listView = $view;

        return $this;
    }

    /**
     * Set the add route to work with in the App\Traits\CanCrud trait.
     *
     * @param string $route
     * @return CanCrudOptions
     */
    public function setAddRoute($route): CanCrudOptions
    {
        $this->addRoute = $route;

        return $this;
    }

    /**
     * Set the add view to work with in the App\Traits\CanCrud trait.
     *
     * @param string $view
     * @return CanCrudOptions
     */
    public function setAddView($view): CanCrudOptions
    {
        $this->addView = $view;

        return $this;
    }

    /**
     * Set the edit route to work with in the App\Traits\CanCrud trait.
     *
     * @param string $route
     * @return CanCrudOptions
     */
    public function setEditRoute($route): CanCrudOptions
    {
        $this->editRoute = $route;

        return $this;
    }

    /**
     * Set the edit view to work with in the App\Traits\CanCrud trait.
     *
     * @param string $view
     * @return CanCrudOptions
     */
    public function setEditView($view): CanCrudOptions
    {
        $this->editView = $view;

        return $this;
    }
}