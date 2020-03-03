const gulp = require('gulp');
const tailwindConfig = "tailwind.config.js";

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
    .src(["assets/src/styles.css"])
    .pipe(postcss([atimport(), tailwindcss(tailwindConfig)]))
    .pipe(
      purgecss({
        content: ["**/*.html", "../../**/*.md"],
        extractors: [
          {
            extractor: TailwindExtractor = (content) => {
                return content.match(/[\w-/:]+(?<!:)/g) || [];
            },
            extensions: ["html", "md"]
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
 * Task: gulp default
 */
gulp.task('default', gulp.series(
    'css'
));

/**
 * Task: gulp watch
 */
gulp.task('watch', function () {
    gulp.watch(["**/*.html", "../../**/*.md", "assets/src/"], gulp.series('css'));
});
