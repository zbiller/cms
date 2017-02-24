<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\ModelNotFoundException;

interface Role
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions();

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
