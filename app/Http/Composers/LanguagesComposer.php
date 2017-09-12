<?php

namespace App\Http\Composers;

use App\Models\Localisation\Language;
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
        if (!translation()->isMultiLanguageEnabled()) {
            return;
        }

        $languages = Language::onlyActive()->get();

        if (!($language = $languages->where('code', app()->getLocale())->first())) {
            $language = $languages->first();
        }

        $view->with([
            'language' => $language,
            'languages' => $languages,
        ]);
    }
}