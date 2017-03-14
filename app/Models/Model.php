<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel
{
    /**
     * List of global scopes to be applied.
     * This property should be defined on children models, containing a list of global scopes classes.
     *
     * @var array
     */
    protected $scopes = [];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        foreach (self::scopes() as $scope) {
            static::addGlobalScope(app($scope));
        }
    }

    /**
     * Get the global scopes list of the child model.
     *
     * @return array
     */
    protected static function scopes()
    {
        return (new static())->scopes;
    }
}