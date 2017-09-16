<?php

namespace App\Traits;

use App\Models\Auth\Permission;
use App\Models\Auth\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

trait HasRoles
{
    use HasPermissions;

    /**
     * A user belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * A user belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    /**
     * Filter the query by the given roles.
     *
     * @param $query
     * @param string|array|Role|Collection $roles
     * @return mixed
     */
    public function scopeWithRoles($query, $roles)
    {
        if ($roles instanceof Role) {
            $roles = [$roles];
        } else {
            $roles = collect($roles)->map(function ($role) {
                if ($role instanceof Role) {
                    return $role;
                }

                return Role::findByName($role);
            });
        }

        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->where(function ($query) use ($roles) {
                foreach ($roles as $role) {
                    $query->orWhere('roles.id', $role->id);
                }
            });
        });
    }

    /**
     * Assign roles to the a user.
     *
     * @param string|array|Role|Collection $roles
     * @return $this
     */
    public function assignRoles($roles)
    {
        try {
            if ($roles instanceof Role) {
                $this->roles()->save($roles);
            } else {
                $this->roles()->saveMany(
                    collect($roles)->flatten()->map(function ($role) {
                        return $this->getRole($role);
                    })->all()
                );

                $this->forgetPermissionsCache();
            }
        } catch (QueryException $e) {
            $this->removeRoles($roles);
            $this->assignRoles($roles);
        }

        return $this;
    }

    /**
     * Remove roles from the a user.
     *
     * @param string|array|Role|Collection $roles
     * @return $this
     */
    public function removeRoles($roles)
    {
        if ($roles instanceof Role) {
            $this->roles()->detach($roles);
        } else {
            $this->roles()->detach(
                (new Collection($roles))->map(function ($role) {
                    return $this->getRole($role);
                })
            );
        }

        return $this;
    }

    /**
     * Sync a user's roles.
     *
     * @param string|array|Role|Collection $roles
     * @return $this
     */
    public function syncRoles($roles)
    {
        $this->roles()->detach();
        $this->assignRoles($roles);

        return $this;
    }

    /**
     * Check if a user has a given role.
     *
     * @param string|Role $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->roles->contains(
            is_string($role) ? 'name' : 'id',
            is_string($role) ? $role : $role->id
        );
    }

    /**
     * Check if a user has any role from a collection of given roles.
     *
     * @param array|Collection $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        if (!$roles || empty($roles)) {
            return true;
        }

        return (bool)(new Collection($roles))->map(function ($role) {
            return $this->getRole($role);
        })->intersect($this->roles)->count();
    }

    /**
     * Check if a user has every role from a collection of given roles.
     *
     * @param array|Collection $roles
     * @return bool
     */
    public function hasAllRoles($roles)
    {
        $collection = collect()->make($roles)->map(function ($role) {
            return $role instanceof Role ? $role->name : $role;
        });

        return $collection == $collection->intersect(
            $this->roles->pluck('name')
        );
    }

    /**
     * Check if a user has a given permission.
     *
     * @param string|Permission $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            try {
                $permission = Permission::findByName($permission);
            } catch (ModelNotFoundException $e) {
                return false;
            }
        }

        return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
    }

    /**
     * Check if a user has any permission from a collection of given permissions.
     *
     * @param array|Collection $permissions
     * @return bool
     */
    public function hasAnyPermission($permissions)
    {
        if (!$permissions || empty($permissions)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a user has every permission from a collection of given permissions.
     *
     * @param array|Collection $permissions
     * @return bool
     */
    public function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get all user's permissions, both direct or via roles.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissions()
    {
        return $this->getDirectPermissions()->merge(
            $this->getPermissionsViaRoles()
        )->sort()->values();
    }

    /**
     * Check if a user has a permission granted via a role assigned.
     *
     * @param string|Permission $permission
     * @return bool
     */
    protected function hasPermissionViaRole($permission)
    {
        if (is_string($permission)) {
            try {
                dd($permission);
                $permission = Permission::findByName($permission);
            } catch (ModelNotFoundException $e) {
                dd($e);
                return false;
            }
        }

        return $permission->roles->count() > 0 && $this->hasAnyRole($permission->roles);
    }

    /**
     * Check ifa user has a permission whose directly attached to it.
     *
     * @param string|Permission $permission
     * @return bool
     */
    protected function hasDirectPermission($permission)
    {
        if (is_string($permission)) {
            try {
                $permission = Permission::findByName($permission);
            } catch (ModelNotFoundException $e) {
                return false;
            }
        }

        return $this->permissions->contains('id', $permission->id);
    }

    /**
     * Get a user's direct permissions.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getDirectPermissions()
    {
        return $this->permissions;
    }

    /**
     * Get a user's permissions assigned via a role.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getPermissionsViaRoles()
    {
        return $this->load('roles', 'roles.permissions')->roles->flatMap(function ($role) {
            return $role->permissions;
        })->sort()->values();
    }


    /**
     * Get a user's role.
     *
     * @param string|array|Role|Collection $roles
     * @return Role|Collection
     */
    protected function getRole($roles)
    {
        if (is_numeric($roles)) {
            return app(Role::class)->find($roles);
        }

        if (is_string($roles)) {
            return app(Role::class)->findByName($roles);
        }

        if (is_array($roles)) {
            return app(Role::class)->whereIn('name', $roles)->get();
        }

        return $roles;
    }
}
