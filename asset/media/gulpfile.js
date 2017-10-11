var gulp = require('gulp');
var sass = require('gulp-sass');
var del = require('del');
var concat = require('gulp-concat');
var rename = require('gulp-rename');
var uglify = require('gulp-uglify');
var gulpMerge = require('gulp-merge');
var compressCss = require('gulp-minify-css');

var paths = {
    scripts: [
        './node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js',
        './node_modules/magnific-popup/dist/jquery.magnific-popup.min.js',
        'js/**/*.js'
    ],
    css: [
        './node_modules/magnific-popup/dist/magnific-popup.css'
    ],
    scss: [
        './node_modules/bootstrap-sass/assets/stylesheets',
        './node_modules/font-awesome/scss',
        'scss/**/*.scss'
    ],
    images: 'img/**/*'
};

// Not all tasks need yarn add delto use streams
// A gulpfile is just another node program and you can use any package available on npm
gulp.task('clean', function () {
    // You can use multiple globbing patterns as you would with `gulp.src`
    return del(['build']);
});

gulp.task('js', function () {
    return gulp.src(paths.scripts)
        .pipe(concat('script.js'))
        .pipe(gulp.dest('build/js'))
        .pipe(rename('script.min.js'))
        .pipe(uglify())
        .pipe(gulp.dest('build/js'));
});

gulp.task('sass', function () {

    var sassStream,
        cssStream;

    sassStream = gulp.src("scss/main.scss")
        .pipe(sass({
            style: 'compressed',
            includePaths: paths.scss
        }).on('error', sass.logError))
        .pipe(gulp.dest('build/css'));

    cssStream = gulp.src(paths.css);

    return gulpMerge(cssStream,sassStream)
        .pipe(concat('all.css'))
        .pipe(compressCss())
        .pipe(gulp.dest('build/css'));

});

gulp.task('fonts', function () {
    return gulp.src([
        './node_modules/font-awesome/fonts/fontawesome-webfont.*',
        './node_modules/bootstrap-sass/assets/fonts/bootstrap/*'
    ])
        .pipe(gulp.dest('build/fonts/'));
});

// Rerun the task when a file changes
gulp.task('watch', function () {
    gulp.watch(paths.scripts, ['js']);
    gulp.watch(paths.scss, ['sass']);
    gulp.watch(paths.images, ['images']);
});

// The default task (called when you run `gulp` from cli)
gulp.task('default', ['watch', 'sass', 'js', 'fonts']);
gulp.task('build', ['sass', 'js', 'fonts']);