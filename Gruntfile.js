module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            build: {
                src: 'resources/assets/js/*.js',
                dest: 'resources/assets/js/main.min.js'
            }
        },
        cssmin: {
            target: {
                files: [{
                    expand: true,
                    cwd: 'resources/assets/css',
                    src: ['*.css', '!*.min.css'],
                    dest: 'resources/assets/css',
                    ext: '.min.css'
                }]
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
                    '!storage/**',
                    '!pluginframe',
                    '!composer.json',
                    '!composer.lock',
                    '!tests/**',
                    '!.dist/**',
                    '!Gruntfile.js',
                    '!TwigStringExtractor.php',
                    '!languages/temp-twig-strings.php',
                    '!package.json',
                    '!package-lock.json',
                    '!README.md',
                    '!tailwind.config.js',
                    '!wiki.txt'
                ],
                dest: '.dist/plugin-frame'
            }
        },
        clean: {
            dist: ['.dist/plugin-frame']
        },
        watch: {
            scripts: {
                files: ['resources/assets/js/*.js'],
                tasks: ['uglify'],
            },
            css: {
                files: ['resources/assets/css/*.css'],
                tasks: ['cssmin'],
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['uglify', 'cssmin']);
    grunt.registerTask('build:prod', ['clean:dist', 'uglify', 'cssmin', 'copy:dist']);
};
