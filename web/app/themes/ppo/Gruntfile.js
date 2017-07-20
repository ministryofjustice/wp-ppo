'use strict';
module.exports = function(grunt) {

    grunt.initConfig({
        less: {
            dist: {
                files: {
                    'assets/css/main.min.css': [
                        'assets/less/app.less'
                    ]
                },
                options: {
                    compress: true,
                    // LESS source map
                    // To enable, set sourceMap to true and update sourceMapRootpath based on your install
                    sourceMap: true,
                    sourceMapFilename: 'assets/css/main.min.css.map',
                    sourceMapRootpath: '/ppo/wp-content/themes/ppo/'
                }
            }
        },
        uglify: {
            dist: {
                files: {
                    'assets/js/scripts.min.js': [
                        'assets/js/plugins/bootstrap/transition.js',
                        'assets/js/plugins/bootstrap/alert.js',
                        'assets/js/plugins/bootstrap/button.js',
                        'assets/js/plugins/bootstrap/carousel.js',
                        'assets/js/plugins/bootstrap/collapse.js',
                        'assets/js/plugins/bootstrap/dropdown.js',
                        'assets/js/plugins/bootstrap/modal.js',
                        'assets/js/plugins/bootstrap/tooltip.js',
                        'assets/js/plugins/bootstrap/popover.js',
                        'assets/js/plugins/bootstrap/scrollspy.js',
                        'assets/js/plugins/bootstrap/tab.js',
                        'assets/js/plugins/bootstrap/affix.js',
                        'assets/js/plugins/*.js',
                        'assets/js/_*.js'
                        //'assets/js/src/*.js'
                    ]
                },
                options: {
                    // JS source map: to enable, uncomment the lines below and update sourceMappingURL based on your install
                    // sourceMap: 'assets/js/scripts.min.js.map',
                    // sourceMappingURL: '/app/themes/roots/assets/js/scripts.min.js.map'
                }
            }
        },
        version: {
            options: {
                file: 'lib/scripts.php',
                css: 'assets/css/main.min.css',
                cssHandle: 'roots_main',
                js: 'assets/js/scripts.min.js',
                jsHandle: 'roots_scripts'
            }
        },
        watch: {
            less: {
                files: [
                    'assets/less/*.less',
                    'assets/less/bootstrap/*.less'
                ],
                tasks: ['less', 'version']
            },
            js: {
                files: [
                    'assets/js/*.js',
                    //'assets/js/src/*.js',
                    '!assets/js/scripts.min.js'
                ],
                tasks: ['uglify', 'version']
            },
            livereload: {
                // Browser live reloading
                // https://github.com/gruntjs/grunt-contrib-watch#live-reloading
                options: {
                    livereload: true
                },
                files: [
                    'assets/css/main.min.css',
                    'assets/js/scripts.min.js',
                    //'templates/*.php',
                    //'*.php',
                    '**/*.php',
                    '!lib/scripts.php'
                ]
            }
        },
        clean: {
            dist: [
                'assets/css/main.min.css',
                'assets/js/scripts.min.js'
            ]
        },
        grunticon: {
            myIcons: {
                files: [{
                        expand: true,
                        cwd: 'assets/icons',
                        src: ['*.svg', '*.png'],
                        dest: "assets/icons/output"
                    }],
                options: {
                }
            }
        }
    });

    // Load tasks
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-wp-version');
    grunt.loadNpmTasks('grunt-grunticon');

    // Register tasks
    grunt.registerTask('default', [
        'clean',
        'less',
        'uglify',
        'version'
//        'grunticon:myIcons'
    ]);
    grunt.registerTask('dev', [
        'watch'
    ]);
};