const gulp = require('gulp');
const tailwindConfig = "tailwind.config.js";

/**
 * Task: gulp vendor-css
 */
 gulp.task("vendor-css", function() {
   const concat = require('gulp-concat');
   const csso = require('gulp-csso');
   const autoprefixer = require('gulp-autoprefixer');

   return gulp
     .src([
          // Swal2
          'node_modules/sweetalert2/dist/sweetalert2.min.css',

          // AnimateCSS
          'node_modules/animate.css/animate.min.css',

           // CodeMirror
          'node_modules/codemirror/lib/codemirror.css',
          'node_modules/codemirror/theme/elegant.css'])
     .pipe(autoprefixer({
         overrideBrowserslist: [
             "last 1 version"
         ],
         cascade: false
     }))
     .pipe(csso())
     .pipe(concat('vendor-build.min.css'))
     .pipe(gulp.dest("assets/dist/css/"));
 });


/**
 * Task: gulp admin-panel-css
 */
gulp.task("admin-panel-css", function() {
  const atimport = require("postcss-import");
  const postcss = require("gulp-postcss");
  const tailwindcss = require("tailwindcss");
  const purgecss = require("gulp-purgecss");
  const concat = require('gulp-concat');
  const csso = require('gulp-csso');
  const sourcemaps = require('gulp-sourcemaps');
  const autoprefixer = require('gulp-autoprefixer');

  return gulp
    .src([
           // Admin Panel CSS
          'assets/src/admin-panel.css'])
    .pipe(postcss([atimport(), tailwindcss(tailwindConfig)]))
    .pipe(
      purgecss({
        content: ["templates/**/*.html"],
        extractors: [
          {
            extractor: TailwindExtractor = (content) => {
                return content.match(/[\w-/:]+(?<!:)/g) || [];
            },
            extensions: ["html"]
          }
        ]
      })
    )
    .pipe(autoprefixer({
        overrideBrowserslist: [
            "last 1 version"
        ],
        cascade: false
    }))
    .pipe(csso())
    .pipe(concat('admin-panel-build.min.css'))
    .pipe(gulp.dest("assets/dist/css/"));
});

/**
 * Task: gulp vendor-js
 */
 gulp.task('vendor-js', function(){
   const sourcemaps = require('gulp-sourcemaps');
   const concat = require('gulp-concat');

   return gulp.src([ // jQuery
                    'node_modules/jquery/dist/jquery.min.js',

                    // Swal2
                    'node_modules/sweetalert2/dist/sweetalert2.min.js',

                    // ParsleyJS Form Validatator
                    'node_modules/parsleyjs/dist/parsley.min.js',

                    // SpeakingURL
                    'node_modules/speakingurl/speakingurl.min.js',

                    // Popper
                    'node_modules/popper.js/dist/umd/popper.min.js',

                    // Tippy
                    'node_modules/tippy.js/dist/tippy-bundle.iife.min.js',

                    // Clipboard
                    'node_modules/clipboard/dist/clipboard.min.js',

                    // CodeMirror
                    'node_modules/codemirror/lib/codemirror.js',
                    'node_modules/codemirror/mode/htmlmixed/htmlmixed.js',
                    'node_modules/codemirror/mode/xml/xml.js',
                    'node_modules/codemirror/mode/javascript/javascript.js',
                    'node_modules/codemirror/mode/php/php.js',
                    'node_modules/codemirror/mode/clike/clike.js',
                    'node_modules/codemirror/mode/yaml/yaml.js'
                 ])
     .pipe(sourcemaps.init())
     .pipe(concat('admin-panel-build.min.js'))
     .pipe(sourcemaps.write())
     .pipe(gulp.dest('assets/dist/js/'));
 });

/**
 * Task: gulp default
 */
gulp.task('default', gulp.series(
    'vendor-css', 'admin-panel-css', 'vendor-js'
));

/**
 * Task: gulp watch
 */
gulp.task('watch', function () {
    gulp.watch(["templates/**/*.html", "assets/src/"], gulp.series('vendor-css', 'admin-panel-css'));
});
