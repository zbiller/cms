<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRoles
{
    /**
     * @var
     */
    protected $roles;

    /**
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, ...$roles)
    {
        $this->setRoles($roles, $request->route()->action);

        if (!auth()->user()->hasAllRoles($this->roles)) {
            return redirect()->route('home');
        }

        return $next($request);
    }

    /**
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
