<?php

namespace App\Contracts;

interface PermissionContract
{
    /**
     * Permission has and belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /**
     * Permission has and belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * Return the permission by it's name.
     *
     * @param string $name
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByName($name);
}
