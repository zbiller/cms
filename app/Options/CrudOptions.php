<?php

namespace App\Options;

use App\Models\Model;

class CrudOptions
{
    public $model;

    public $listRoute;
    public $listView;

    public $addRoute;
    public $addView;

    public $editRoute;
    public $editView;

    /**
     * @return CrudOptions
     */
    public static function instance(): CrudOptions
    {
        return new static();
    }

    /**
     * @param Model $model
     * @return CrudOptions
     */
    public function setModel(Model $model): CrudOptions
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param string $route
     * @return CrudOptions
     */
    public function setListRoute($route): CrudOptions
    {
        $this->listRoute = $route;

        return $this;
    }

    /**
     * @param string $view
     * @return CrudOptions
     */
    public function setListView($view): CrudOptions
    {
        $this->listView = $view;

        return $this;
    }

    /**
     * @param string $route
     * @return CrudOptions
     */
    public function setAddRoute($route): CrudOptions
    {
        $this->addRoute = $route;

        return $this;
    }

    /**
     * @param string $view
     * @return CrudOptions
     */
    public function setAddView($view): CrudOptions
    {
        $this->addView = $view;

        return $this;
    }

    /**
     * @param string $route
     * @return CrudOptions
     */
    public function setEditRoute($route): CrudOptions
    {
        $this->editRoute = $route;

        return $this;
    }

    /**
     * @param string $view
     * @return CrudOptions
     */
    public function setEditView($view): CrudOptions
    {
        $this->editView = $view;

        return $this;
    }
}