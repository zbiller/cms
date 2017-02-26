<?php

namespace App\Helpers\Menu;

use Closure;
use Illuminate\Support\Collection;

class Menu
{
    /**
     * @var Collection
     */
    protected $items;

    /**
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
     * @param Closure|null $callback
     * @return $this
     */
    public function filter(Closure $callback = null)
    {
        $this->items = $this->items->filter($callback);

        return $this;
    }

    /**
     * @param Closure $callback
     */
    public function add(Closure $callback)
    {
        $item = new Item();

        call_user_func($callback, $item);

        $this->items->push($item);
    }

    /**
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
     * @return Collection
     */
    public function roots()
    {
        return $this->items->filter(function ($item) {
            return $item->parent === null;
        });
    }

    /**
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