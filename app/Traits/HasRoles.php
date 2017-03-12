<?php

namespace App\Traits;

use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection;

trait HasRoles
{
    use HasPermissions;
    use RefreshesCache;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    /**
     * @param $query
     * @param string|array|Role|Collection $roles
     * @return mixed
     */
    public function scopeRole($query, $roles)
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

                $this->forgetCache();
            }
        } catch (QueryException $e) {
            $this->removeRoles($roles);
            $this->assignRoles($roles);
        }

        return $this;
    }

    /**
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
     * @param array|Collection $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        return (bool)(new Collection($roles))->map(function ($role) {
            return $this->getRole($role);
        })->intersect($this->roles)->count();
    }

    /**
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
     * @param array|Collection $permissions
     * @return bool
     */
    public function hasAnyPermission($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
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
     * @return \Illuminate\Support\Collection
     */
    public function getPermissions()
    {
        return $this->getDirectPermissions()->merge(
            $this->getPermissionsViaRoles()
        )->sort()->values();
    }

    /**
     * @param string|Permission $permission
     * @return bool
     */
    protected function hasPermissionViaRole($permission)
    {
        if (is_string($permission)) {
            try {
                $permission = Permission::findByName($permission);
            } catch (ModelNotFoundException $e) {
                return false;
            }
        }

        return $permission->roles->count() > 0 && $this->hasAnyRole($permission->roles);
    }

    /**
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
     * @return \Illuminate\Support\Collection
     */
    protected function getDirectPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getPermissionsViaRoles()
    {
        return $this->load('roles', 'roles.permissions')->roles->flatMap(function ($role) {
            return $role->permissions;
        })->sort()->values();
    }


    /**
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
