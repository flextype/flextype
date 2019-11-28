//
// Flextype Gulp.js
// (c) Sergey Romanenko <http://romanenko.digital>
//

const { series, src, dest } = require('gulp');
const del = require('del');
const csso = require('gulp-csso');
const concat = require('gulp-concat');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const sass = require('gulp-sass');

function moveBootstrapCss() {
    return src('node_modules/bootstrap/dist/css/bootstrap.min.css')
        .pipe(concat('1.min.css'))
        .pipe(dest('assets/dist/css/tmp'));
}

function buldDefaultCss() {
      return src('assets/scss/default.scss')
          .pipe(sass().on('error', sass.logError))
          .pipe(concat('2.min.css'))
          .pipe(dest('assets/dist/css/tmp'));
}

function moveJqueryJs() {
    return src('node_modules/jquery/dist/jquery.min.js')
        .pipe(concat('1.min.js'))
        .pipe(dest('assets/dist/js/tmp'));
}

function moveBootstrapJs() {
    return src('node_modules/bootstrap/dist/js/bootstrap.min.js')
        .pipe(concat('2.min.js'))
        .pipe(dest('assets/dist/js/tmp'));
}

function moveSimpleLightboxJs() {
    return src('node_modules/simplelightbox/dist/simple-lightbox.min.js')
        .pipe(concat('3.min.js'))
        .pipe(dest('assets/dist/js/tmp'));
}

function mergeCss() {
    return src('assets/dist/css/tmp/**')
        .pipe(autoprefixer({
            overrideBrowserslist: [
                "last 1 version"
            ],
            cascade: false
        }))
        .pipe(csso())
        .pipe(concat('build.min.css'))
        .pipe(dest('assets/dist/css/'));
}

function mergeJs() {
    return src('assets/dist/js/tmp/**')
        .pipe(concat('build.min.js'))
        .pipe(dest('assets/dist/js/'));
}

function cleanTmpCss() {
    return del('assets/dist/css/tmp/');
}

function cleanTmpJs() {
    return del('assets/dist/js/tmp/');
}

exports.default = series(moveBootstrapCss,
                         buldDefaultCss,
                         mergeCss,
                         cleanTmpCss,
                         moveJqueryJs,
                         moveBootstrapJs,
                         mergeJs,
                         cleanTmpJs);
