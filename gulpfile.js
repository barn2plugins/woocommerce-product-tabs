// Config.
var rootPath = './';

// Env.
require('dotenv').config();

// Gulp.
var gulp = require( 'gulp' );

// File system.
var fs = require('fs');

// Package.
var pkg = JSON.parse(fs.readFileSync('./package.json'));

// Delete.
var del = require('del');

// Zip.
var zip = require('gulp-zip');

// Browser sync.
var browserSync = require('browser-sync').create();

// Deploy files list.
var deploy_files_list = [
	'admin/**',
	'includes/**',
	'languages/**',
	'public/**',
	'readme.txt',
	pkg.main_file
];

// Watch.
gulp.task( 'watch', function() {
    browserSync.init({
        proxy: process.env.DEV_SERVER_URL,
        open: true
    });

    // Watch PHP files.
    gulp.watch( rootPath + '**/**/*.php' ).on('change',browserSync.reload);
});

// Clean deploy folder.
gulp.task('clean:deploy', function() {
    return del('deploy')
});

// Copy to deploy folder.
gulp.task('copy:deploy', function() {
	return gulp.src(deploy_files_list,{base:'.'})
	    .pipe(gulp.dest('deploy/' + pkg.name))
	    .pipe(zip(pkg.name + '.zip'))
	    .pipe(gulp.dest('deploy'))
});

// Tasks.
gulp.task( 'default', gulp.series('watch'));

gulp.task( 'deploy', gulp.series('clean:deploy', 'copy:deploy'));
