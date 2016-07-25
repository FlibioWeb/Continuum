var gulp  = require('gulp'),
    sass = require('gulp-sass'),
    cssnano = require('gulp-cssnano'),
    eol = require('gulp-eol'),

    input  = {
      'sass': 'style/scss/**/*.scss',
    },

    output = {
      'stylesheets': 'style',
    };

gulp.task('default', ['build-css', 'watch']);

gulp.task('build-css', function() {
  return gulp.src(input.sass)
    .pipe(sass())
    .pipe(cssnano())
    .pipe(eol())
    .pipe(gulp.dest(output.stylesheets));
});

gulp.task('watch', function() {
  gulp.watch(input.sass, ['build-css']);
});
