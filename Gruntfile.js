module.exports = function(grunt) {
	'use strict';

	grunt.initConfig({
		pkg: grunt.file.readJSON( 'package.json' ),

		wp_deploy: {
			deploy: {
				options: {
					plugin_slug: '<%= pkg.name %>',
					plugin_main_file: '<%= pkg.main_file %>',
					svn_user: 'wpconcern',
					build_dir: 'deploy/<%= pkg.name %>',
					assets_dir: '.wordpress-org'
				},
			}
		},
	});

	grunt.loadNpmTasks('grunt-wp-deploy');

	grunt.registerTask('wpdeploy', ['wp_deploy']);
};
