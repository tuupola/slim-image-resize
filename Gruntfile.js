module.exports = function(grunt) {
    "use strict";

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        watch: {
            js: {
                files: ["*.js"],
                tasks: ["jshint"]
            },
            php: {
                files: ["src/**/*.php"],
                tasks: ["phplint"]
            }
        },
        jshint: {
            files: ["*.js"],
            options: {
                jshintrc: ".jshintrc"
            }
        },
        phplint: {
            options: {
                swapPath: "/tmp"
            },
            all: ["src/**/*.php"]
        }
    });

    require("load-grunt-tasks")(grunt);

    /*
    grunt.registerTask("build", ["concat", "uglify", "cssmin"]);
    grunt.registerTask("test", ["jshint", "jasmine"]);
    grunt.registerTask("testphp", ["phplint", "phpunit"]);
    grunt.registerTask("default", ["testphp", "test", "build"]);
    */
    grunt.registerTask("default", ["jshint", "phplint"]);

};