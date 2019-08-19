'use strict';

const {src, dest, watch, series, parallel} = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    cssnano = require('gulp-cssnano'),
    bs = require('browser-sync').create();

function scssTask() {
    return src('src/scss/**/*.+(scss|sass)')
            .pipe(sass(
                    {
                        outputStyle: 'expanded',
                        includePaths: ["node_modules/bootstrap/scss/"]
                    }
            ))
//    .pipe(concat('styles.css'))
            .pipe(autoprefixer(['last 15 versions', '>1%', 'ie 8']))
//    .pipe(cssnano())
            .pipe(dest('htdocs'))
            .pipe(bs.stream());
};

function highchartsTask() {
    return src([
        'node_modules/highcharts/highstock.js',
        'node_modules/highcharts/highcharts.js',
        'node_modules/highcharts/modules/exporting.js'
    ])
            .pipe(dest('htdocs/libs/highcharts'));
}

function jstreeTask() {
    return src([
        'node_modules/jstree/dist/jstree.min.js',
        'node_modules/jstree/dist/themes/default/throbber.gif',
        'node_modules/jstree/dist/themes/default/32px.png',
        'node_modules/jstree/dist/themes/default/40px.png',
        'node_modules/jstree/dist/themes/default/style.min.css'
    ])
            .pipe(dest('htdocs/libs/jstree'));
}

function jqueryTask() {
    return src([
        'node_modules/jquery/dist/jquery.min.js'
    ])
            .pipe(dest('htdocs/libs/jquery'));
}

function bootstrapTask() {
    return src([
        'node_modules/bootstrap/dist/js/bootstrap.min.js'
    ])
            .pipe(dest('htdocs/libs/bootstrap'));
}

function html5shivTask() {
    return src([
        'node_modules/html5shiv/dist/html5shiv.min.js'
    ])
            .pipe(dest('htdocs/libs'))
            .pipe(bs.stream());
}

function watchTask() {
    bs.init({
        proxy: "phpmd.localhost"
    });
    watch('src/scss/**/*.+(scss|sass)', scssTask);
    watch('htdocs/**/*.+(php|html|css|js)', bs.reload);
}

function defaultTask(cb) {
    console.log('Нет действия по умолчанию.');
    cb();
}

exports.bootstrap = bootstrapTask;
exports.scss = scssTask;
exports.highcharts = highchartsTask;
exports.jquery = jqueryTask;
exports.jstree = jstreeTask;
exports.html5shiv = html5shivTask;
exports.jslibs = parallel(jqueryTask,jstreeTask,bootstrapTask,highchartsTask,html5shivTask);
exports.watch = watchTask;
exports.default = defaultTask;
