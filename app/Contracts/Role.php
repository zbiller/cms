<?php

namespace App\Contracts;

interface Role
{
    /**
     * Role has and belongs to many  permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions();

    /**
     * Role has and belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * Return the role by it's name.
     *
     * @param string $name
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByName($name);
}
