<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermissions
{
    /**
     * The permissions required for the user to have in order to continue.
     *
     * @var
     */
    protected $permissions;

    /**
     * Handle the middleware's login.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$permissions
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$permissions)
    {
        $this->setPermissions($permissions, $request->route()->action);

        if (!auth()->user()->isSuper() && !auth()->user()->hasAllPermissions($this->permissions)) {
            flash()->error('Permission denied!');
            return back();
        }

        return $next($request);
    }

    /**
     * Set the permissions.
     * Please note that this middleware supports permissions to be passed in multiple ways.
     * 1. As a middleware parameter, using the "," as delimiter.
     * 2. As a route action custom parameter called "permissions", using the "," as delimiter.
     * In the end, permissions from both assigning ways are merged.
     *
     * @param array $permissions
     * @param array $action
     */
    protected function setPermissions($permissions = [], $action = [])
    {
        $this->permissions = $permissions;

        if (isset($action['permissions'])) {
            $this->permissions = array_unique(array_merge(
                $this->permissions,
                explode(',', $action['permissions'])
            ), SORT_REGULAR);
        }
    }
}
