<?php

namespace App\Http\Composers;

use App\Models\Localisation\Language;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\View\View;

class LanguagesComposer
{
    /**
     * Construct the admin menu.
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        if (config('language.enable_multi_language') !== true) {
            return;
        }

        $languages = Language::all();

        try {
            $language = Language::whereCode(app()->getLocale())->firstOrFail();
        } catch (ModelNotFoundException $e) {
            $language = Language::onlyDefault()->first();
        }

        $view->with([
            'language' => $language,
            'languages' => $languages,
        ]);
    }
}