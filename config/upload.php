<?php

return [

    /**
     * Storage settings.
     */
    'storage' => [

        /**
         * The storage disk where to upload the files.
         *
         * By default, the "uploads" storage disk is used.
         * Please note that the disk is already defined.
         *
         * If the disk is not defined, you have to define it yourself.
         * To do this, follow these steps:
         *
         * 1. Go to config/filesystems under "disks" and create a new disk called "uploads", with the following options.
         * 2. Verify if the /storage/uploads directory exists and if not, create it with a .gitignore file inside.
         * 3. Run the artisan command: uploads:link, to create a symlink between the storage and public directories.
         */
        'disk' => 'uploads',

    ],

    /**
     * Database settings.
     */
    'database' => [

        /**
         * Determine if the uploaded files' details will be saved to the database.
         *
         * This is encouraged, every uploaded file would have more details about it.
         * Also, if this is saved, applying the HasUploads trait on models, unlocks certain query scopes.
         *
         * To disable this, set the value to "false" (bool).
         */
        'save' => true,

        /**
         * The database table name where the details of the uploaded files will be stored.
         *
         * By default, a migration creating this table exists.
         * However, if you choose to change this value, you will have to create another migration.
         * Don't forget to delete the old table.
         *
         */
        'table' => 'uploads',

    ],

    /*'images' => [
        'convert_to' => 'jpg',

        'styles' => [
            'small' => [
                'width' => '100',
                'height' => '100'
            ],
            'big' => [
                'width' => '500',
                'height' => '500'
            ]
        ]
    ],

    'videos' => [

        'convert_to' => 'mp4',

        'generate_thumbnail' => true,

        'styles' => [
            'small' => [
                'width' => '100',
                'height' => '100'
            ],
            'big' => [
                'width' => '500',
                'height' => '500'
            ]
        ]

    ],

    'audio' => [

        'convert_to' => 'mp3'

    ]*/

];