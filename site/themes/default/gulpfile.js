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

function moveAnimateCss() {
    return src('node_modules/animate.css/animate.min.css')
        .pipe(concat('2.min.css'))
        .pipe(dest('assets/dist/css/tmp'));
}

function moveSimpleLightbox() {
    return src('node_modules/simplelightbox/dist/simplelightbox.min.css')
        .pipe(concat('3.min.css'))
        .pipe(dest('assets/dist/css/tmp'));
}

function buldDefaultCss() {
      return src('assets/scss/default.scss')
          .pipe(sass().on('error', sass.logError))
          .pipe(concat('4.min.css'))
          .pipe(dest('assets/dist/css/tmp'));
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

function cleanTmp() {
    return del('assets/dist/css/tmp/');
}

exports.default = series(moveBootstrapCss, moveAnimateCss, moveSimpleLightbox, buldDefaultCss, mergeCss, cleanTmp);
