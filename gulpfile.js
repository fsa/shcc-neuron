var gulp = require('gulp'),
        sass = require('gulp-sass'),
        autoprefixer = require('gulp-autoprefixer'),
        concat = require('gulp-concat'),
        uglify = require('gulp-uglifyjs'),
        cssnano = require('gulp-cssnano'),
        rename = require('gulp-rename'),
        del = require('del'),
        browserSync = require('browser-sync');

gulp.task('default', function () {
    console.log('Нет действия по умолчанию.');
});

gulp.task('watch', ['browser-sync'], function () {
    gulp.watch('src/scss/**/*.+(scss|sass)', ['scss']);
    gulp.watch('htdocs/**/*.+(php|html|css|js)', browserSync.reload);
});

gulp.task('browser-sync', ['scss'], function () {
    browserSync({
        proxy: "phpmd.localhost"
    });
});

gulp.task('scss', function () {
    return gulp.src('src/scss/**/*.+(scss|sass)')
            .pipe(sass(
                    {
                        outputStyle: 'expanded',
                        includePaths: ["node_modules/bootstrap/scss/"]
                    }
            ))
//    .pipe(concat('styles.css'))
            .pipe(autoprefixer(['last 15 versions', '>1%', 'ie 8']))
//    .pipe(cssnano())
            .pipe(gulp.dest('htdocs'))
            .pipe(browserSync.reload({stream: true}));
});

gulp.task('jslibs', ['jquery', 'leaflet', 'highcharts'], function () {
    console.log('Копирование мелких библиотек js');
    return gulp.src([
        'node_modules/html5shiv/dist/html5shiv.min.js',
    ])
            .pipe(gulp.dest('htdocs/libs'))
            .pipe(browserSync.reload({stream: true}));
});

gulp.task('leaflet', function () {
    console.log('Копирование Leaflet');
    return gulp.src([
        'node_modules/leaflet/dist/leaflet.js',
        'node_modules/leaflet/dist/leaflet.css',
        'node_modules/leaflet/dist/**/*.png'
    ])
            .pipe(gulp.dest('htdocs/libs/leaflet'));
    ;
});

gulp.task('highcharts', function () {
    console.log('Копирование highcharts');
    return gulp.src([
        'node_modules/highcharts/highstock.js',
        'node_modules/highcharts/highcharts.js',
        'node_modules/highcharts/modules/exporting.js'
    ])
            .pipe(gulp.dest('htdocs/libs/highcharts'));
});

gulp.task('jquery', function () {
    console.log('Копирование jquery');
    return gulp.src([
        'node_modules/jquery/dist/jquery.min.js',
    ])
            .pipe(gulp.dest('htdocs/libs/jquery'));
});