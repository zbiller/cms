<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NotAuthenticated
{
    /**
     * @var array
     */
    protected $except = [
        'admin/logout',
    ];

    /**
     * @param Request $request
     * @param Closure $next
     * @param string $route
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $route = 'home')
    {
        if ($this->isException($request)) {
            return $next($request);
        }

        if (auth()->check()) {
            return redirect()->route($route);
        }

        return $next($request);
    }

    /**
     * @param  Request  $request
     * @return bool
     */
    protected function isException($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
