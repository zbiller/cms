<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermissions
{
    /**
     * @var
     */
    protected $permissions;

    /**
     * @param Request $request
     * @param Closure $next
     * @param string ...$permissions
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$permissions)
    {
        $this->setPermissions($permissions, $request->route()->action);

        if (!auth()->user()->hasAllPermissions($this->permissions)) {
            return redirect()->route('admin');
        }

        return $next($request);
    }

    /**
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
