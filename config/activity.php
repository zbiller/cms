<?php

return [

    /**
     * Flag indicating whether or not activities should be logged throughout the app.
     * If set to false, no activities will be saved to the database.
     */
    'enabled' => env('ENABLE_ACTIVITY_LOG', true),

    /**
     * This option accepts an integer, representing the number of days.
     *
     * When logging a new activity into the database, records older than the number of days specified here will be deleted.
     * Also, the same thing happens when executing the command: php artisan activity:clean
     *
     * If set to "null" or "0", no past activities will be deleted whatsoever.
     */
    'delete_records_older_than' => 100,

];