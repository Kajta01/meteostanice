var gulp = require('gulp');
var cleanCSS = require('gulp-clean-css');
var replace = require('gulp-replace');
var rename = require("gulp-rename");
var concat = require('gulp-concat');
var minify = require('gulp-minify');
var less = require('gulp-less');

gulp.task('default', ['build-public-css', 'build-public-js', 'build-admin-css', 'build-admin-js']);

gulp.task('watch', function() {
	gulp.watch('public/**/*.css', ['build-public-css']);
	gulp.watch('public/**/*.js', ['build-public-js']);
  gulp.watch('admin/**/*.less', ['build-admin-css']);
	gulp.watch('admin/**/*.js', ['build-admin-js']);
});

gulp.task('build-public-css', function() {
	gulp.src("public/fonts/*")
    .pipe(gulp.dest("build/fonts"));
  return gulp.src([
      'public/js/jsgrid/jsgrid.min.css',
      'public/css/jsgrid-theme-wptables.css',
      'public/css/wptables-public.css'
    ])
    .pipe(concat("wptables.min.css"))
    .pipe(replace('url("../../fonts/', 'url("../fonts/'))
    .pipe(cleanCSS())
    .pipe(gulp.dest('build/css'));
});

gulp.task('build-public-js', function() {
  return gulp.src([
      'public/js/jsgrid/jsgrid.min.js',
      'public/js/numeral/numeral.min.js',
      'public/js/jsgrid/jsgrid-extension.js',
      'public/js/wptables-public.js',
    ])
    .pipe(concat('wptables.min.js'))
    .pipe(minify({
      ext: {
        min: '.js'
      },
      noSource: true
    }))
    .pipe(gulp.dest('build/js'));
});

gulp.task('build-admin-css', function() {
	return gulp.src('admin/css/wptables-admin.less')
    .pipe(less())
    .pipe(cleanCSS())
    .pipe(rename('wptables-admin.min.css'))
    .pipe(gulp.dest('build/css'));
});

gulp.task('build-admin-js', function() {
	gulp.src('admin/js/wptables-tinymce-plugin.js')
    //.pipe(rename('wptables-admin.min.js'))
    .pipe(minify({
      ext: {
        min: '.min.js'
      },
      noSource: true
    }))
    .on('error', swallowError)
    .pipe(gulp.dest('build/js'));
	return gulp.src('admin/js/wptables-admin.js')
    .pipe(rename('wptables-admin.min.js'))
    .pipe(minify({
      ext: {
        min: '.js'
      },
      noSource: true
    }))
    .on('error', swallowError)
    .pipe(gulp.dest('build/js'));
});

function swallowError (error) {
  // If you want details of the error in the console
  console.log(error.toString())
  this.emit('end')
}
