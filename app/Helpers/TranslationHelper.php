<?php

namespace App\Helpers;

class TranslationHelper
{
    /**
     * Determine if multi language behavior is enabled for the application.
     * This is done comparing the value of the "config/language.php" -> "enable_multi_language".
     * Please note that this value can be set from inside the ".env" file, "ENABLE_MULTI_LANGUAGE" key.
     *
     * @return bool
     */
    public function isMultiLanguageEnabled()
    {
        return config('translation.enable_translations') === true;
    }

    /**
     * Determine if the entity being processed is translatable.
     * This is done comparing the value of the "config/language.php" -> "is_translatable_entity".
     * Please note that this value is automatically assigned from the "App\Http\Middleware\IsTranslatable" middleware.
     *
     * @return bool
     */
    public function isEntityTranslatable()
    {
        return config('translation.is_translatable_entity') === true;
    }
}