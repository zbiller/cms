<?php

namespace App\Helpers\Menu;

use Closure;
use Illuminate\Support\Collection;

class Menu
{
    /**
     * The menu items.
     *
     * @var Collection
     */
    protected $items;

    /**
     * Generate a new menu.
     *
     * @param Closure $callback
     * @return $this
     */
    public function make(Closure $callback)
    {
        $this->items = new Collection();

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
        $item = new Item();

        call_user_func($callback, $item);

        $this->items->push($item);
    }

    /**
     * Container for generating children menu items inside a parent node.
     * Add a new child menu item via a callback for a parent node.
     * The callback should generate individual menu items.
     * Setting the properties using methods from App\Helpers\Menu\Item
     *
     * @param Item $parent
     * @param Closure $callback
     */
    public function child(Item $parent, Closure $callback)
    {
        $item = new Item();
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
     * @param Item $parent
     * @return Collection
     */
    public function children(Item $parent)
    {
        return $this->items->filter(function ($item) use ($parent) {
            return $item->parent == $parent->id;
        });
    }
}