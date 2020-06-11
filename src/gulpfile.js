'use strict';

const {src, dest, watch, series, parallel} = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    cssnano = require('gulp-cssnano'),
    bs = require('browser-sync').create();

function scssTask() {
    return src('scss/**/*.+(scss|sass)')
            .pipe(sass(
                    {
                        outputStyle: 'expanded',
                        includePaths: ["node_modules/bootstrap/scss/"]
                    }
            ))
            .pipe(autoprefixer(['last 15 versions', '>1%', 'ie 8']))
            .pipe(cssnano())
            .pipe(dest('../htdocs'))
            .pipe(bs.stream());
};

function highchartsTask() {
    return src([
        'node_modules/highcharts/highstock.js',
        'node_modules/highcharts/highstock.js.map',
        'node_modules/highcharts/highcharts.js',
        'node_modules/highcharts/highcharts.js.map',
        'node_modules/highcharts/modules/exporting.js',
        'node_modules/highcharts/modules/exporting.js.map'
    ])
            .pipe(dest('../htdocs/libs/highcharts'));
}

function jstreeTask() {
    return src([
        'node_modules/jstree/dist/jstree.min.js',
        'node_modules/jstree/dist/themes/default/throbber.gif',
        'node_modules/jstree/dist/themes/default/32px.png',
        'node_modules/jstree/dist/themes/default/40px.png',
        'node_modules/jstree/dist/themes/default/style.min.css'
    ])
            .pipe(dest('../htdocs/libs/jstree'));
}

function jqueryTask() {
    return src([
        'node_modules/jquery/dist/jquery.min.js'
    ])
            .pipe(dest('../htdocs/libs/jquery'));
}

function bootstrapTask() {
    return src([
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.min.js.map'       
    ])
            .pipe(dest('../htdocs/libs/bootstrap'));
}

function watchTask() {
    bs.init({
        proxy: "shcc.localhost"
    });
    watch('scss/**/*.+(scss|sass)', scssTask);
    watch('../htdocs/**/*.+(php|html|css|js)', bs.reload);
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
exports.jslibs = parallel(jqueryTask,jstreeTask,bootstrapTask,highchartsTask);
exports.watch = watchTask;
exports.default = defaultTask;
