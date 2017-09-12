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
        if (!translation()->isMultiLanguageEnabled()) {
            return $next($request);
        }

        $default = Language::onlyDefault()->first();

        if (app()->getLocale() != $default->code && $this->onEntityCreate()) {
            app()->setLocale($default->code);
            session()->put('locale', $default->code);
            flash()->warning(
                'You are trying to add an entity in other language than the default one!' .
                '<br /><br />' .
                'The language has been switched back to the default one.'
            );
        }

        app()->make('config')->set('translation.is_translatable_entity', true);

        return $next($request);
    }

    /**
     * Determine if the current request is for "creating" an entity record.
     *
     * @return bool
     */
    protected function onEntityCreate()
    {
        return ends_with(Route::getCurrentRoute()->getActionName(), '@create');
    }
}