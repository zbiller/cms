<?php

namespace App\Http\Middleware;

use App\Models\Seo\Redirect;
use Closure;

class RedirectRequests
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $redirect = Redirect::findValidOrNull($request->path());

        if ($redirect && $redirect->exists) {
            return redirect($redirect->new_url, $redirect->status);
        }

        return $next($request);
    }
}