<?php

return [

    /**
     * Flag indicating if multi-language behavior should be present at all on the site.
     * If this is set to false, the multi-language feature will not work (be present), regardless of your other settings.
     */
    'enable_multi_language' => env('ENABLE_MULTI_LANGUAGE', false),

    /**
     * Flag indicating if a particular entity is translatable.
     * Based on this flag, the "languages drop-down" will or will not be shown in the admin.
     *
     * The hardcoded value below should always be "false".
     * The value will be automatically set to "true" by the "App\Http\Middleware\IsTranslatable" middleware.
     *
     * Because of this, do not forget to apply the "is.translatable" middleware defined in the "App\Http\Kernel".
     * On every route for the entity you wish to make translatable.
     */
    'is_translatable' => false,

];