<?php

namespace App\Http\Middleware;

use Closure;

class PersistLocale
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('language.enable_multi_language') === true && session()->has('locale')) {
            app()->setLocale(session('locale'));
        }

        return $next($request);
    }
}