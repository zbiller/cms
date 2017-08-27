let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
    .copyDirectory('resources/assets/fonts', 'public/fonts')
    .copyDirectory('resources/assets/images', 'public/images')
    .copyDirectory('resources/assets/js/scripts', 'public/js/scripts')

    /**
     * Compile ADMIN sass files.
     */
    .sass('resources/assets/sass/admin/main.scss', 'public/css/admin/app.css')

    /**
     * Compile FRONT sass files.
     */
    .sass('resources/assets/sass/front/main.scss', 'public/css/front/app.css')

    /**
     * Concatenate LIBRARY css files.
     */
    .styles([
        'resources/assets/css/admin/libs/jquery.css',
        'resources/assets/css/admin/libs/dropzone.css',
        'resources/assets/css/admin/libs/jstree.css',
        'resources/assets/css/admin/libs/jcrop.css',
        'resources/assets/css/admin/libs/chosen.css',
        'resources/assets/css/admin/libs/tooltip.css',
        'resources/assets/css/admin/libs/colorpicker.css',
        'resources/assets/css/admin/libs/timepicker.css',
        'resources/assets/css/admin/libs/scroll.css',
        'resources/assets/css/admin/libs/icons.css'
    ], 'public/css/admin/libs.css')

    /**
     * Minify LIBRARY js files.
     */
    .scripts([
        'resources/assets/js/admin/libs/jquery.js',
        'resources/assets/js/admin/libs/jquery-ui.js',
        'resources/assets/js/admin/libs/jquery-transport.js',
        'resources/assets/js/admin/libs/bootstrap.js',
        'resources/assets/js/admin/libs/jcrop.js',
        'resources/assets/js/admin/libs/scroll.js',
        'resources/assets/js/admin/libs/chosen.js',
        'resources/assets/js/admin/libs/timepicker.js',
        'resources/assets/js/admin/libs/colorpicker.js',
        'resources/assets/js/admin/libs/generator.js',
        'resources/assets/js/admin/libs/dropzone.js',
        'resources/assets/js/admin/libs/tooltip.js',
        'resources/assets/js/admin/libs/upload.js',
        'resources/assets/js/admin/libs/jstree.js',
        'resources/assets/js/admin/libs/tablednd.js',
        './public/vendor/jsvalidation/js/jsvalidation.js'
    ], 'public/js/admin/libs.js')

    /**
     * Bundle ADMIN js files.
     */
    .scripts([
        'resources/assets/js/admin/helpers.js',
        'resources/assets/js/admin/main.js'
    ], 'public/js/admin/app.js')

    /**
     * Bundle FRONT js files.
     */
    .scripts([
        'resources/assets/js/front/main.js'
    ], 'public/js/front/app.js')

    /**
     * Version the FINAL css & js files.
     */
    .version([
        'public/css/admin/libs.css',
        'public/css/admin/app.css',
        'public/css/front/app.css',
        'public/js/admin/libs.js',
        'public/js/admin/app.js',
        'public/js/front/app.js'
    ]);