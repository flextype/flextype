const gulp = require('gulp');
const tailwindConfig = "tailwind.config.js";

/**
 * Custom PurgeCSS Extractor
 * https://github.com/FullHuman/purgecss
 */
class TailwindExtractor {
  static extract(content) {
    return content.match(/[\w-/:]+(?<!:)/g) || [];
  }
}

/**
 * Task: gulp css
 */
gulp.task("css", function() {
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
          'assets/src/admin-panel.css',

          // Select2
         'node_modules/select2/dist/css/select2.min.css',

          // CodeMirror
         'node_modules/codemirror/lib/codemirror.css'])
    .pipe(postcss([atimport(), tailwindcss(tailwindConfig)]))
    .pipe(
      purgecss({
        content: ["**/*.php", "**/*.html", "../../**/*.md"],
        extractors: [
          {
            extractor: TailwindExtractor,
            extensions: ["html", "md", 'php']
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
    .pipe(concat('build.min.css'))
    .pipe(gulp.dest("assets/dist/css/"));
});

/**
 * Task: gulp js
 */
 gulp.task('js', function(){
   const sourcemaps = require('gulp-sourcemaps');
   const concat = require('gulp-concat');

   return gulp.src([ // jQuery
                    'node_modules/jquery/dist/jquery.min.js',

                    // Select2
                    'node_modules/select2/dist/js/select2.min.js',

                     // FontAwesome
                     'node_modules/@fortawesome/fontawesome-free/js/all.min.js',

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
     .pipe(concat('build.min.js'))
     .pipe(sourcemaps.write())
     .pipe(gulp.dest('assets/dist/js/'));
 });


gulp.task('watch', function () {
    gulp.watch(["**/*.php", "**/*.html", "../../**/*.md", "assets/src/"], gulp.series('css', 'js'));
});
