module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        sass: {
            backend_sass: {
                options: {
                    style: 'expanded'
                },
                files: {
                    'resources/assets/admin/css/fonts.css': 'resources/assets/admin/sass/fonts.scss',
                    'resources/assets/admin/css/helpers.css': 'resources/assets/admin/sass/helpers.scss',
                    'resources/assets/admin/css/general.css': 'resources/assets/admin/sass/general.scss',
                    'resources/assets/admin/css/header.css': 'resources/assets/admin/sass/header.scss',
                    'resources/assets/admin/css/sidebar.css': 'resources/assets/admin/sass/sidebar.scss',
                    'resources/assets/admin/css/footer.css': 'resources/assets/admin/sass/footer.scss',
                    'resources/assets/admin/css/content.css': 'resources/assets/admin/sass/content.scss',
                    'resources/assets/admin/css/login.css': 'resources/assets/admin/sass/login.scss',
                    'resources/assets/admin/css/filters.css': 'resources/assets/admin/sass/filters.scss',
                    'resources/assets/admin/css/upload.css': 'resources/assets/admin/sass/upload.scss',
                    'resources/assets/admin/css/tabs.css': 'resources/assets/admin/sass/tabs.scss',
                    'resources/assets/admin/css/list.css': 'resources/assets/admin/sass/list.scss',
                    'resources/assets/admin/css/view.css': 'resources/assets/admin/sass/view.scss'
                }
            }
        },
        concat: {
            backend_js: {
                src: [
                    'resources/assets/admin/js/libs/jquery.js',
                    'resources/assets/admin/js/libs/jquery-ui.js',
                    'resources/assets/admin/js/libs/bootstrap.js',
                    'resources/assets/admin/js/libs/scroll.js',
                    'resources/assets/admin/js/libs/chosen.js',
                    'resources/assets/admin/js/libs/timepicker.js',
                    'resources/assets/admin/js/libs/colorpicker.js',
                    'resources/assets/admin/js/libs/generator.js',
                    'resources/assets/admin/js/libs/dropzone.js',
                    'resources/assets/admin/js/main.js',
                    'public/vendor/jsvalidation/js/jsvalidation.js'
                ],
                dest: 'public/build/admin/js/super.js'
            },
            backend_css: {
                src: [
                    'resources/assets/admin/css/libs/jquery-ui.css',
                    'resources/assets/admin/css/libs/icons.css',
                    'resources/assets/admin/css/libs/scroll.css',
                    'resources/assets/admin/css/libs/chosen.css',
                    'resources/assets/admin/css/libs/timepicker.css',
                    'resources/assets/admin/css/libs/colorpicker.css',
                    'resources/assets/admin/css/libs/dropzone.css',
                    'resources/assets/admin/css/fonts.css',
                    'resources/assets/admin/css/helpers.css',
                    'resources/assets/admin/css/general.css',
                    'resources/assets/admin/css/header.css',
                    'resources/assets/admin/css/sidebar.css',
                    'resources/assets/admin/css/footer.css',
                    'resources/assets/admin/css/content.css',
                    'resources/assets/admin/css/login.css',
                    'resources/assets/admin/css/filters.css',
                    'resources/assets/admin/css/upload.css',
                    'resources/assets/admin/css/tabs.css',
                    'resources/assets/admin/css/list.css',
                    'resources/assets/admin/css/view.css'
                ],
                dest: 'public/build/admin/css/super.css'
            }
        },
        uglify: {
            backend_js: {
                files: {
                    'public/build/admin/js/super.min.js': ['public/build/admin/js/super.js']
                }
            }
        },
        cssmin: {
            options: {
                rebase: false
            },
            backend_css: {
                files: {
                    'public/build/admin/css/super.min.css': ['public/build/admin/css/super.css']
                }
            }
        },
        watch: {
            backend: {
                files: [
                    'resources/assets/admin/sass/*.scss',
                    'resources/assets/admin/css/*.css',
                    'resources/assets/admin/js/*.js'
                ],
                tasks: [
                    'sass',
                    'concat',
                    'uglify',
                    'cssmin'
                ]
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', [
        'sass',
        'concat',
        'uglify',
        'cssmin'
    ]);
};