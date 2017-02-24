<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\ModelNotFoundException;

interface Permission
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * @param string $name
     * @throws ModelNotFoundException
     */
    public static function findByName($name);
}
