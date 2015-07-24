var gulp = require('gulp');
var less = require('gulp-less');
var path = require('path');
var rename = require("gulp-rename");

// Путь до скина TinyMCE
var skin_path = './';

// Дефолтная задача
gulp.task('default', ['less']);

// Собирает LESS файлы
gulp.task('less', ['less:skin', 'less:content']);

// Собирает скин TinyMCE
gulp.task('less:skin', function () {
    return gulp.src(skin_path + 'skin.modern.dev.less')
        .pipe(less())
        .pipe(rename('skin.min.css'))
        .pipe(gulp.dest(skin_path));
});

// Собирает стили контента TinyMCE
gulp.task('less:content', function () {
    return gulp.src(skin_path + 'Content.less')
        .pipe(less())
        .pipe(rename('content.min.css'))
        .pipe(gulp.dest(skin_path));
});

// Следит за изменениями файлов скина
gulp.task('watch', function() {
    gulp.watch(skin_path + '**/*.less', ['less']);
});