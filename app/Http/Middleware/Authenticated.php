<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Authenticated
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string $route
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next, $route = 'login')
    {
        if (!auth()->check()) {
            return redirect()->route($route);
        }

        return $next($request);
    }
}
