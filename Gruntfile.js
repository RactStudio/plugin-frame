module.exports = function (pf) {
    'use strict';
    pf.initConfig({
        pkg: pf.file.readJSON('package.json'),
        webpack: {
            options: require('./webpack.config.js'), // Load Webpack config
            build: {} // Run Webpack
        },
        uglify: {
            alpine: {
                files: {
                    'resources/assets/js/alpine.min.js': ['resources/assets/js/alpine.bundle.js']
                }
            },
            flowbite: {
                files: {
                    'resources/assets/js/flowbite.min.js': ['resources/assets/js/flowbite.bundle.js']
                }
            },
            lucide: {
                files: {
                    'resources/assets/js/lucide.min.js': ['resources/assets/js/lucide.bundle.js']
                }
            },
            admin: {
                files: {
                    'resources/assets/js/admin.min.js': ['resources/assets/js/admin.bundle.js']
                }
            },
            frontend: {
                files: {
                    'resources/assets/js/frontend.min.js': ['resources/assets/js/frontend.bundle.js']
                }
            }
        },
        cssmin: {
            admin: {
                files: {
                    'resources/assets/css/admin.min.css': ['resources/assets/css/*.css', '!resources/assets/css/*.min.css', '!resources/assets/css/tailwind*.css']
                }
            },
            frontend: {
                files: {
                    'resources/assets/css/frontend.min.css': ['resources/assets/css/*.css', '!resources/assets/css/*.min.css', '!resources/assets/css/tailwind*.css']
                }
            }
        },
        copy: {
            dist: {
                expand: true,
                src: [
                    '**',
                    '!.DS_Store',
                    '!Thumbs.db',
                    '!desktop.ini',
                    '!node_modules/**',
                    '!cache/**',
                    '!logs/**',
                    '!pf/**',
                    '!pf',
                    '!composer.json',
                    '!composer.lock',
                    '!tests/**',
                    '!.dist/**',
                    '!Gruntfile.js',
                    '!package.json',
                    '!package-lock.json',
                    '!README.md',
                    '!tailwind.config.js',
                    '!TASK.txt',
                    '!webpack.config.js',
                    '!translate-gen.mjs',
                    '!translate-twig.mjs',
                    '!wiki.txt'
                ],
                dest: '.dist/plugin-frame'
            }
        },
        clean: {
            dist: ['.dist/plugin-frame'],
            bundles: ['resources/assets/js/*.bundle.*'] // Clean up Webpack bundles after build
        },
        watch: {
            scripts: {
                files: ['resources/assets/js/*.js'],
                tasks: ['webpack', 'uglify']
            },
            css: {
                files: ['resources/assets/css/*.css'],
                tasks: ['cssmin']
            }
        }
    });

    pf.loadNpmTasks('grunt-webpack');
    pf.loadNpmTasks('grunt-contrib-uglify');
    pf.loadNpmTasks('grunt-contrib-cssmin');
    pf.loadNpmTasks('grunt-contrib-copy');
    pf.loadNpmTasks('grunt-contrib-clean');
    pf.loadNpmTasks('grunt-contrib-watch');

    pf.registerTask('default', ['webpack', 'uglify', 'cssmin', 'clean:bundles']);
    pf.registerTask('build:prod', ['clean:dist', 'webpack', 'uglify', 'cssmin', 'clean:bundles', 'copy:dist']);
};