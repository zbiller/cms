<?php

namespace App\Helpers;

use App\Models\Cms\Menu;
use Closure;
use Illuminate\Support\Collection;

class MenuHelper
{
    /**
     * The menu items.
     *
     * @var Collection
     */
    protected $items;

    /**
     * Fetch the root menu items for a location.
     *
     * @param string $location
     * @return mixed
     */
    public function get($location)
    {
        return Menu::with('menuable')->onlyActive()->whereIsRoot()->whereLocation($location)->defaultOrder()->get();
    }

    /**
     * Generate a new menu.
     *
     * @param Closure $callback
     * @return $this
     */
    public function make(Closure $callback)
    {
        $this->items = collect();

        call_user_func($callback, $this);

        return $this;
    }

    /**
     * Filter the menu items based on a callback.
     *
     * @param Closure|null $callback
     * @return $this
     */
    public function filter(Closure $callback = null)
    {
        $this->items = $this->items->filter($callback);

        return $this;
    }

    /**
     * Add a new menu item via a callback.
     * The callback should generate individual menu items.
     * Setting the properties using methods from App\Helpers\Menu\Item
     *
     * @param Closure $callback
     */
    public function add(Closure $callback)
    {
        $item = new MenuItem;

        call_user_func($callback, $item);

        $this->items->push($item);
    }

    /**
     * Container for generating children menu items inside a parent node.
     * Add a new child menu item via a callback for a parent node.
     * The callback should generate individual menu items.
     * Setting the properties using methods from App\Helpers\Menu\Item
     *
     * @param MenuItem $parent
     * @param Closure $callback
     */
    public function child(MenuItem $parent, Closure $callback)
    {
        $item = new MenuItem;
        $item->parent = $parent->id;

        call_user_func($callback, $item);

        $this->items->push($item);
    }

    /**
     * Get all parent menu items.
     *
     * @return Collection
     */
    public function roots()
    {
        return $this->items->filter(function ($item) {
            return $item->parent === null;
        });
    }

    /**
     * Get the children menu items corresponding to a parent.
     *
     * @param MenuItem $parent
     * @return Collection
     */
    public function children(MenuItem $parent)
    {
        return $this->items->filter(function ($item) use ($parent) {
            return $item->parent == $parent->id;
        });
    }
}

/**
 * Class MenuItem
 * @package App\Helpers
 */
class MenuItem
{
    /**
     * The id of the menu item.
     *
     * @var
     */
    public $id;

    /**
     * The parent item for a menu item.
     *
     * @var
     */
    public $parent;

    /**
     * The name of a menu item.
     *
     * @var
     */
    public $name;

    /**
     * The url of a menu item.
     *
     * @var
     */
    public $url;

    /**
     * The active identifier for a menu item.
     *
     * @var
     */
    public $active = [];

    /**
     * The permissions that a menu item requires met for it to dislpay.
     *
     * @var array
     */
    public $permissions = [];

    /**
     * Container for additional menu item properties.
     *
     * @var array
     */
    public $data = [];

    /**
     * Set an id for the current menu item.
     *
     * @set $id
     */
    public function __construct()
    {
        $this->id = uniqid(rand(), true);
    }

    /**
     * Set|Get the name property for the current menu item.
     *
     * @param string|null $name
     * @return $this|string
     */
    public function name($name = null)
    {
        if ($name === null) {
            return $this->name;
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Set|Get the url property for the current menu item.
     *
     * @param string|null $url
     * @return $this|string
     */
    public function url($url = null)
    {
        if ($url === null) {
            return $this->url;
        }

        $this->url = $url;

        return $this;
    }

    /**
     * Set|Get the active property for the current menu item.
     *
     * @param string|null $active
     * @return $this|bool
     */
    public function active(...$active)
    {
        if (!$active) {
            foreach ($this->active as $active) {
                if (
                    (str_contains($active, '*') && starts_with(request()->path(), trim($active, '*/'))) ||
                    request()->path() == $active
                ) {
                    return true;
                    break;
                }
            }

            return false;
        }

        $this->active = $active;

        return $this;
    }

    /**
     * Set|Get the permissions property for the current menu item.
     *
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
     * Set|Get the data property for the current menu item.
     *
     * @param string|null $key
     * @param string|null $value
     * @return $this|array|string
     */
    public function data($key = null, $value = null)
    {
        if ($value === null) {
            return $key ? $this->data[$key] : $this->data;
        }

        $this->data[$key] = $value;

        return $this;
    }
}