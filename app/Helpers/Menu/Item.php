<?php

namespace App\Helpers\Menu;

class Item
{
    /**
     * @var
     */
    public $id;

    /**
     * @var
     */
    public $parent;

    /**
     * @var
     */
    public $name;

    /**
     * @var
     */
    public $url;

    /**
     * @var
     */
    public $active;

    /**
     * @var array
     */
    public $permissions = [];

    /**
     * @var array
     */
    public $data = [];

    /**
     * @set $id
     */
    public function __construct()
    {
        $this->id = uniqid(rand());
    }

    /**
     * @param string|null $name
     * @return $this|string
     */
    public function name($name = null)
    {
        if (!$name) {
            return $this->name;
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @param string|null $url
     * @return $this|string
     */
    public function url($url = null)
    {
        if (!$url) {
            return $this->url;
        }

        $this->url = $url;

        return $this;
    }

    /**
     * @param array|null $permissions
     * @return $this|array
     */
    public function permissions(...$permissions)
    {
        if (!$permissions) {
            return $this->permissions;
        }

        $this->permissions = $permissions;

        return $this;
    }

    /**
     * @param string|null $key
     * @param string|null $value
     * @return $this|array|string
     */
    public function data($key = null, $value = null)
    {
        if (!$value) {
            return $key ? $this->data[$key] : $this->data;
        }

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param string|null $active
     * @return $this|bool
     */
    public function active($active = null)
    {
        if (!$active) {
            return str_contains($this->active, '*') ?
                starts_with(request()->path(), trim($this->active, '*/')) :
                request()->path() == $this->active;
        }

        $this->active = $active;

        return $this;
    }
}