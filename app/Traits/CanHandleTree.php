<?php

namespace App\Traits;

trait CanHandleTree
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\HasTreeOptions file.
     *
     * @var CanCacheOptions
     */
    protected $hasTreeOptions;

    /**
     * The method used for setting the refresh cache options.
     * This method should be called inside the model using this trait.
     * Inside the method, you should set all the refresh cache options.
     * This can be achieved using the methods from App\Options\CanCacheOptions.
     *
     * @return CanCacheOptions
     */
    abstract public function getCanCacheOptions(): CanCacheOptions;


}
