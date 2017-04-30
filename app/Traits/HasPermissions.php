<?php

namespace App\Traits;

use App\Models\Auth\Permission;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection;

trait HasPermissions
{
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

            $this->forgetCache();
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

        $this->forgetCache();

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
    protected function getPermission($permissions)
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
}