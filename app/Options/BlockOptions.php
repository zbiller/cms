<?php

namespace App\Options;

use App\Models\Model;

class BlockOptions
{
    /**
     * @var array
     */
    public $locations;

    /**
     * The cache key in use.
     *
     * @var Model|string
     */
    public $inherit;

    /**
     * Get a fresh instance of this class.
     *
     * @return BlockOptions
     */
    public static function instance(): BlockOptions
    {
        return new static();
    }

    /**
     * Set the locations to work with in the App\Traits\HasBlocks trait
     *
     * @param callable|array|string
     * @return BlockOptions
     */
    public function setLocations($locations): BlockOptions
    {
        switch ($locations) {
            case is_callable($locations):
                $this->locations = call_user_func($locations);
                break;
            case is_string($locations):
                $this->locations = explode(',', $locations);
                break;
            case is_array($locations):
                $this->locations = $locations;
                break;
        }

       return $this;
    }

    /**
     * Set the inherit to work with in the App\Traits\HasBlocks trait.
     *
     * @param callable|string $inherit
     * @return BlockOptions
     */
    public function inheritFrom($inherit): BlockOptions
    {
        if (is_callable($inherit)) {
            $this->inherit = call_user_func($inherit);
        } else {
            $this->inherit = $inherit;
        }


        return $this;
    }
}