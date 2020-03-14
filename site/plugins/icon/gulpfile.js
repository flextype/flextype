const gulp = require('gulp');

/**
 * Task: gulp fontawesome-icons
 */
gulp.task('fontawesome-icons', function(){
    return gulp.src(['node_modules/@fortawesome/fontawesome-free/svgs/**/*'])
        .pipe(gulp.dest('assets/dist/fontawesome/svgs'));
});

/**
 * Task: gulp default
 */
gulp.task('default', gulp.series(
    'fontawesome-icons'
));
