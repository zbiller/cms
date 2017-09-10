<?php

namespace App\Http\Middleware;

use App\Models\Localisation\Language;
use Closure;
use Route;

class IsTranslatable
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (config('language.enable_multi_language') !== true) {
            return $next($request);
        }

        $default = Language::onlyDefault()->first();

        if (app()->getLocale() != $default->code && ends_with(Route::getCurrentRoute()->getActionName(), '@create')) {
            app()->setLocale($default->code);
            session()->put('locale', $default->code);
            flash()->warning('You are trying to add an entity in other language than the default one! The language has been switched back to the default one.');
        }

        app()->make('config')->set('language.is_translatable', true);

        return $next($request);
    }
}