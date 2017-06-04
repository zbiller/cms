<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRoles
{
    /**
     * The roles required for the user to have in order to continue.
     *
     * @var
     */
    protected $roles;

    /**
     * Handle the middleware's login.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $this->setRoles($roles, $request->route()->action);

        if (!auth()->user()->isSuper() && !auth()->user()->hasAllRoles($this->roles)) {
            session()->flash('flash_error', 'Not authorized!');
            return redirect('/');
        }

        return $next($request);
    }

    /**
     * Set the roles.
     * Please note that this middleware supports roles to be passed in multiple ways.
     * 1. As a middleware parameter, using the "," as delimiter.
     * 2. As a route action custom parameter called "roles", using the "," as delimiter.
     * In the end, roles from both assigning ways are merged.
     *
     * @param array $roles
     * @param array $action
     */
    protected function setRoles($roles = [], $action = [])
    {
        $this->roles = $roles;

        if (isset($action['roles'])) {
            $this->roles = array_unique(array_merge(
                $this->roles,
                explode(',', $action['roles'])
            ), SORT_REGULAR);
        }
    }
}
