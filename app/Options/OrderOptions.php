<?php

namespace App\Options;

class OrderOptions
{
    /**
     * The database field that will contain the order position of a record in set.
     *
     * @var string
     */
    public $orderColumn;

    /**
     * Flag indicating whether or not automatic ordering on creating should be done.
     *
     * @var bool
     */
    public $orderWhenCreating = true;

    /**
     * Get a fresh instance of this class.
     *
     * @return OrderOptions
     */
    public static function instance(): OrderOptions
    {
        return new static();
    }

    /**
     * Set the $orderColumn to work with in the App\Traits\IsOrderable trait.
     *
     * @param string $column
     * @return OrderOptions
     */
    public function setOrderColumn($column): OrderOptions
    {
        $this->orderColumn = $column;

        return $this;
    }

    /**
     * Set the $orderWhenCreating to work with in the App\Traits\IsOrderable trait.
     *
     * @return OrderOptions
     */
    public function doNotOrderWhenCreating(): OrderOptions
    {
        $this->orderWhenCreating = false;

        return $this;
    }
}