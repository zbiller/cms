<?php

return [

    /**
     * How many records to display on one page inside the admin.
     */
    'per_page' => 10,

    /**
     * The list of exceptions that are soft (not throwable).
     *
     * When an exception defined here is caught by the cruding functionality.
     * Instead of throwing it, the exception will be softly handled.
     *
     * This means that either an error message will appear or a redirect will happen.
     */
    'soft_exceptions' => [
        \App\Exceptions\CrudException::class,
        \App\Exceptions\UploadException::class,
        \App\Exceptions\DraftException::class,
        \App\Exceptions\RevisionException::class,
        \App\Exceptions\UrlException::class,
        \App\Exceptions\DuplicateException::class,
        \App\Exceptions\OrderException::class,
        \App\Exceptions\CartException::class,
    ],

];