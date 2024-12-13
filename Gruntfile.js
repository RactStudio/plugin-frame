module.exports = function (pf) {
    'use strict';
    pf.initConfig({
        pkg: pf.file.readJSON('package.json'),
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
                    '!logs/**',
                    '!pf/**',
                    '!pf',
                    '!cloudflared.exe',
                    '!cloudflared_tunnel.log',
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
                tasks: ['uglify']
            },
            css: {
                files: ['resources/assets/css/*.css'],
                tasks: ['cssmin']
            }
        }
    });

    pf.loadNpmTasks('grunt-contrib-uglify');
    pf.loadNpmTasks('grunt-contrib-cssmin');
    pf.loadNpmTasks('grunt-contrib-copy');
    pf.loadNpmTasks('grunt-contrib-clean');
    pf.loadNpmTasks('grunt-contrib-watch');

    pf.registerTask('default', ['uglify', 'cssmin']);
    pf.registerTask('build:prod', ['clean:dist', 'uglify', 'cssmin', 'copy:dist']);
};
