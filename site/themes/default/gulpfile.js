//
// Flextype Gulp.js
// (c) Sergey Romanenko <http://romanenko.digital>
//

var Promise = require("es6-promise").Promise,
    gulp = require('gulp'),
    csso = require('gulp-csso'),
    concat = require('gulp-concat'),
    sourcemaps = require('gulp-sourcemaps'),
    autoprefixer = require('gulp-autoprefixer'),
    sass = require('gulp-sass');

gulp.task('default-css', function() {
    return gulp.src('assets/scss/default.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(autoprefixer({
            browsers: ['last 2 versions'],
            cascade: false
        }))
        .pipe(csso())
        .pipe(concat('default.min.css'))
        .pipe(gulp.dest('assets/dist/css/'));
});

gulp.task('js', function(){
  return gulp.src(['node_modules/jquery/dist/jquery.min.js', 'node_modules/bootstrap/dist/js/bootstrap.min.js'])
    .pipe(sourcemaps.init())
    .pipe(concat('default.min.js'))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest('assets/dist/js/'));
});

gulp.task('bootstrap-css', function() {
    return gulp.src('node_modules/bootstrap/dist/css/bootstrap.min.css')
        .pipe(gulp.dest('assets/dist/css/'));
});

gulp.task('animate-css', function() {
    return gulp.src('node_modules/animate.css/animate.min.css')
        .pipe(gulp.dest('assets/dist/css/'));
});

gulp.task('simplelightbox-css', function() {
    return gulp.src('node_modules/simplelightbox/dist/simplelightbox.min.css')
        .pipe(gulp.dest('assets/dist/css/'));
});

gulp.task('simplelightbox-js', function() {
    return gulp.src('node_modules/simplelightbox/dist/simple-lightbox.min.js')
        .pipe(gulp.dest('assets/dist/js/'));
});

gulp.task('default', ['default-css', 'js', 'bootstrap-css', 'animate-css', 'simplelightbox-css', 'simplelightbox-js']);
