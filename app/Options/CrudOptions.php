<?php

namespace App\Options;

use App\Models\Model;

class CrudOptions
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
    public $listRouteParameters;
    public $listView;

    /**
     * The add route and view as strings.
     *
     * @var
     */
    public $addRoute;
    public $addRouteParameters;
    public $addView;

    /**
     * The edit route and view as strings.
     *
     * @var
     */
    public $editRoute;
    public $editRouteParameters;
    public $editView;

    /**
     * The deleted route and view as strings
     *
     * @var
     */
    public $deletedRoute;
    public $deletedRouteParameters;
    public $deletedView;

    /**
     * Get a fresh instance of this class.
     *
     * @return CrudOptions
     */
    public static function instance(): CrudOptions
    {
        return new static();
    }

    /**
     * Set the model to work with in the App\Traits\CanCrud trait.
     *
     * @param Model $model
     * @return CrudOptions
     */
    public function setModel(Model $model): CrudOptions
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set the list route to work with in the App\Traits\CanCrud trait.
     *
     * @param string $route
     * @param array $parameters
     * @return CrudOptions
     */
    public function setListRoute($route, array $parameters = []): CrudOptions
    {
        $this->listRoute = $route;
        $this->listRouteParameters = $parameters;

        return $this;
    }

    /**
     * Set the list view to work with in the App\Traits\CanCrud trait.
     *
     * @param string $view
     * @return CrudOptions
     */
    public function setListView($view): CrudOptions
    {
        $this->listView = $view;

        return $this;
    }

    /**
     * Set the add route to work with in the App\Traits\CanCrud trait.
     *
     * @param string $route
     * @param array $parameters
     * @return CrudOptions
     */
    public function setAddRoute($route, array $parameters = []): CrudOptions
    {
        $this->addRoute = $route;
        $this->addRouteParameters = $parameters;

        return $this;
    }

    /**
     * Set the add view to work with in the App\Traits\CanCrud trait.
     *
     * @param string $view
     * @return CrudOptions
     */
    public function setAddView($view): CrudOptions
    {
        $this->addView = $view;

        return $this;
    }

    /**
     * Set the edit route to work with in the App\Traits\CanCrud trait.
     *
     * @param string $route
     * @param array $parameters
     * @return CrudOptions
     */
    public function setEditRoute($route, array $parameters = []): CrudOptions
    {
        $this->editRoute = $route;
        $this->editRouteParameters = $parameters;

        return $this;
    }

    /**
     * Set the edit view to work with in the App\Traits\CanCrud trait.
     *
     * @param string $view
     * @return CrudOptions
     */
    public function setEditView($view): CrudOptions
    {
        $this->editView = $view;

        return $this;
    }

    /**
     * Set the edit route to work with in the App\Traits\CanCrud trait.
     *
     * @param string $route
     * @param array $parameters
     * @return CrudOptions
     */
    public function setDeletedRoute($route, array $parameters = []): CrudOptions
    {
        $this->deletedRoute = $route;
        $this->deletedRouteParameters = $parameters;

        return $this;
    }

    /**
     * Set the edit view to work with in the App\Traits\CanCrud trait.
     *
     * @param string $view
     * @return CrudOptions
     */
    public function setDeletedView($view): CrudOptions
    {
        $this->deletedView = $view;

        return $this;
    }
}