<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticated
{
    /**
     * The request paths ignoring this middleware.
     *
     * @var array
     */
    protected $except = [];

    /**
     * Handle the middleware's login.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $route
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $route = 'login')
    {
        if ($this->isException($request)) {
            return $next($request);
        }

        if (!auth()->check()) {
            return redirect()->route($route);
        }

        return $next($request);
    }

    /**
     * Establish if request path is an exception or not.
     *
     * @param  Request  $request
     * @return bool
     */
    protected function isException($request)
    {
        foreach ($this->except as $except) {
            if ($request->is($except == '/' ? $except : trim($except, '/'))) {
                return true;
            }
        }

        return false;
    }
}