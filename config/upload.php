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

    /**
     * Image settings.
     */
    'images' => [

        /**
         * The maximum size allowed for uploaded image files in MB.
         *
         * If any integer value is specified, files larger than this defined size won't be uploaded.
         * To allow all image files of all sizes to be uploaded, specify the "null" value for this option.
         */
        'max_size' => 50,

        /**
         * The allowed extensions for video files.
         * All video extensions can be found in App\Services\Upload::$videos.
         *
         * You can specify allowed extensions by using an array, or a comma "," separated string of extensions.
         * To allow uploading any video files, specify the "null" value for this option.
         */
        'allowed_extensions' => [
            'jpeg', 'jpg', 'png', 'gif', 'bmp',
        ],

        /**
         * The quality at which to save the uploaded images.
         *
         * Here you can specify an integer value between 1 and 100.
         * If no value is specified (eg. null), then tha uploaded image's quality will be set to 90.
         */
        'quality' => 100,

        /**
         * The styles to create from the original uploaded image.
         * You can specify multiple styles, as array.
         *
         * Specify the "ratio" = true individually on each style, to let the uploader know you want to preserve the original ratio.
         * If ratio preserving is enabled, the image will first be re-sized and the cropped.
         * If ratio preserving is disabled, the image will only be re-sized at the width and height specified.
         */
        'styles' => [
            /*'sample_table_name' => [
                'sample_table_field' => [
                    'sample_style_one' => [
                        'width' => '200',
                        'height' => '100',
                        'ratio' => true,
                    ],
                    'sample_style_two' => [
                        'width' => '100',
                        'height' => '200',
                        'ratio' => false,
                    ]
                ]
            ]*/
        ]
    ],

    /**
     * Video settings.
     */
    'videos' => [

        /**
         * The maximum size allowed for uploaded video files in MB.
         *
         * If any integer value is specified, files larger than this defined size won't be uploaded.
         * To allow all video files of all sizes to be uploaded, specify the "null" value for this option.
         */
        'max_size' => 50,

        /**
         * The allowed extensions for video files.
         * All video extensions can be found in App\Services\Upload::$videos.
         *
         * You can specify allowed extensions by using an array, or a comma "," separated string of extensions.
         * To allow uploading any video files, specify the "null" value for this option.
         */
        'allowed_extensions' => [
            'mp4', 'flv', 'avi', 'mov', 'webm', 'mpeg', 'mpg', 'mkv', 'acc',
        ],

        /**
         * Flag that on video upload to generate thumbnails as well or not (true | false).
         *
         * Thumbnail will be generated from the first second of the uploaded video.
         * All thumbnails will be stored as images having the name {video_file}_thumbnail.jpg.
         */
        'generate_thumbnails' => true,

        /**
         * How many thumbnails should be generated for a video.
         *
         * Keep in mind that if this option is invalid (ex: 0, null, ''), thumbnails won't be generated.
         * This is happening regardless the "generate_thumbnails" options.
         */
        'thumbnails_number' => 3,

    ],

    /**
     * Audio settings.
     */
    'audios' => [

        /**
         * The maximum size allowed for uploaded audio files in MB.
         *
         * If any integer value is specified, files larger than this defined size won't be uploaded.
         * To allow all audio files of all sizes to be uploaded, specify the "null" value for this option.
         */
        'max_size' => 30,

        /**
         * The allowed extensions for audio files.
         * All audio extensions can be found in App\Services\Upload::$audios.
         *
         * You can specify allowed extensions by using an array, or a comma "," separated string of extensions.
         * To allow uploading any audio files, specify the "null" value for this option.
         */
        'allowed_extensions' => [
            'mp3', 'aac', 'wav', 'wma', 'oga', 'flac',
        ]

    ],

    /**
     * Files settings.
     */
    'files' => [

        /**
         * The maximum size allowed for uploaded normal files in MB.
         *
         * If any integer value is specified, files larger than this defined size won't be uploaded.
         * To allow all files of all sizes to be uploaded, specify the "null" value for this option.
         */
        'max_size' => 10,

        /**
         * The allowed extensions for normal files.
         *
         * You can specify allowed extensions by using an array, or a comma "," separated string of extensions.
         * To allow uploading any audio files, specify the "null" value for this option.
         */
        'allowed_extensions' => null,

    ],

];