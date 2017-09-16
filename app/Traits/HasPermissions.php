<?php

namespace App\Traits;

use App\Models\Auth\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;

trait HasPermissions
{
    use HasPermissionsCache;

    /**
     * @param string|array|Permission|Collection $permissions
     * @return HasPermissions
     */
    public function grantPermission($permissions)
    {
        try {
            if ($permissions instanceof Permission) {
                $this->permissions()->save($permissions);
            } else {
                $this->permissions()->saveMany(
                    collect($permissions)->flatten()->map(function ($permission) {
                        return $this->getPermission($permission);
                    })->all()
                );
            }

            $this->forgetPermissionsCache();
        } catch (QueryException $e) {
            $this->revokePermission($permissions);
            $this->grantPermission($permissions);
        }

        return $this;
    }

    /**
     * @param string|array|Permission|Collection $permissions
     * @return $this
     */
    public function revokePermission($permissions)
    {
        if ($permissions instanceof Permission) {
            $this->permissions()->detach($permissions);
        } else {
            $this->permissions()->detach(
                (new Collection($permissions))->map(function ($permission) {
                    return $this->getPermission($permission);
                })
            );
        }

        $this->forgetPermissionsCache();

        return $this;
    }

    /**
     * @param string|array|Permission|Collection $permissions
     * @return $this
     */
    public function syncPermissions($permissions)
    {
        $this->permissions()->detach();
        $this->grantPermission($permissions);

        return $this;
    }

    /**
     * @param string|array|Permission|Collection $permissions
     * @return mixed
     */
    public function getPermission($permissions)
    {
        if (is_numeric($permissions)) {
            return app(Permission::class)->find($permissions);
        }

        if (is_string($permissions)) {
            return app(Permission::class)->findByName($permissions);
        }

        if (is_array($permissions)) {
            return app(Permission::class)->whereIn('name', $permissions)->get();
        }

        return $permissions;
    }

    /**
     * @return static
     */
    public static function getAllGuards()
    {
        $guards = [];

        foreach (config('auth.guards') as $guard => $options) {
            $guards[$guard] = title_case($guard);
        }

        return $guards;
    }

    /**
     * @return static
     */
    public static function getDefaultGuard()
    {
        return config('auth.defaults.guard');
    }
}