const elixir = require('laravel-elixir');
require('laravel-elixir-minify-html');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir(function (mix) {
    mix
        .copy('resources/assets/fonts', 'public/build/assets/fonts')
        .copy('resources/assets/img', 'public/build/assets/img')
        .copy('resources/assets/plugins', 'public/build/assets/plugins')

        .sass([
            'admin/fonts.scss',
            'admin/helpers.scss',
            'admin/general.scss',
            'admin/header.scss',
            'admin/sidebar.scss',
            'admin/footer.scss',
            'admin/content.scss',
            'admin/login.scss',
            'admin/filters.scss',
            'admin/tabs.scss',
            'admin/list.scss',
            'admin/view.scss',
            'admin/popup.scss',
            'admin/upload.scss',
            'admin/analytics.scss',
            'admin/custom.scss'
        ], 'resources/assets/css/admin/main.css')

        .styles([
            'libs/chosen.css',
            'libs/colorpicker.css',
            'libs/dropzone.css',
            'libs/icons.css',
            'libs/jquery.css',
            'libs/scroll.css',
            'libs/timepicker.css',
            'libs/tooltip.css',
            'libs/jstree.css',
            'admin/main.css'
        ], 'public/assets/css/admin/app.css')

        .styles([
            'front/icons.css',
            'front/bootstrap.css',
            'front/animate.css',
            'front/swiper.css',
            'front/layout.css'
        ], 'public/assets/css/front/app.css')

        .scripts([
            'libs/jquery.js',
            'libs/jquery-ui.js',
            'libs/jquery-transport.js',
            'libs/bootstrap.js',
            'libs/scroll.js',
            'libs/chosen.js',
            'libs/timepicker.js',
            'libs/colorpicker.js',
            'libs/generator.js',
            'libs/dropzone.js',
            'libs/tooltip.js',
            'libs/upload.js',
            'libs/jstree.js',
            'libs/tablednd.js',
            'admin/helpers.js',
            'admin/main.js',
            './public/vendor/jsvalidation/js/jsvalidation.js'
        ], 'public/assets/js/admin/app.js')

        .scripts([
            'front/jquery.js',
            'front/migrate.js',
            'front/bootstrap.js',
            'front/easing.js',
            'front/back-to-top.js',
            'front/scroll.js',
            'front/wow.js',
            'front/swiper.js',
            'front/masonry.js',
            'front/images-loaded.js',
            'front/layout.js'
        ], 'public/assets/js/front/app.js')

        .version([
            'assets/css/admin/app.css',
            'assets/js/admin/app.js',
            'assets/css/front/app.css',
            'assets/js/front/app.js'
        ])

        .html(
            'storage/framework/views/*',
            'storage/framework/views/',
            {
                collapseWhitespace: true,
                removeAttributeQuotes: true,
                removeComments: true,
                minifyJS: true
            }
        );
});