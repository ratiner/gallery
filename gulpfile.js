var gulp = require('gulp'),
    	gp_concat = require('gulp-concat'),
		gp_uglify = require('gulp-uglify'),
		gp_sourcemaps = require('gulp-sourcemaps');
	
gulp.task('debug', function(){
    return gulp.src(['src/config.js', 'src/app-src.js', 'src/**/*.js'])
        .pipe(gp_sourcemaps.init())
        .pipe(gp_concat('app.js'))
        //.pipe(gp_uglify())
        //.pipe(gp_sourcemaps.write())
        .pipe(gulp.dest('app'));
});

gulp.task('release', function(){
    return gulp.src(['src/config.js', 'src/app-src.js', 'src/**/*.js'])
        .pipe(gp_sourcemaps.init())
        .pipe(gp_concat('app.js'))
        .pipe(gp_uglify({
            mangle: true
        }))
        .pipe(gp_sourcemaps.write('/'))
        .pipe(gulp.dest('app'));
});
