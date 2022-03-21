"use strict";

const { src, dest, watch, series, parallel } = require("gulp"),
    sass = require("gulp-dart-sass"),
    postcss = require("gulp-postcss"),
    browserSync = require("browser-sync").create();

function scssTask() {
    return src("src/scss/**/*.+(scss|sass)")
        .pipe(
            sass({
                outputStyle: "expanded",
                includePaths: ["node_modules/bootstrap/scss/"],
            }).on("error", function (err) {
                log.error(err.message);
            })
        )
        .pipe(postcss())
        .pipe(dest("webroot"))
        .pipe(browserSync.stream());
}

function highchartsTask() {
    return src([
        "node_modules/highcharts/highstock.js",
        "node_modules/highcharts/highstock.js.map",
        "node_modules/highcharts/highcharts.js",
        "node_modules/highcharts/highcharts.js.map",
        "node_modules/highcharts/modules/exporting.js",
        "node_modules/highcharts/modules/exporting.js.map",
    ]).pipe(dest("webroot/libs/highcharts"));
}

function bootstrapTask() {
    return src([
        "node_modules/bootstrap/dist/js/bootstrap.min.js",
        "node_modules/bootstrap/dist/js/bootstrap.min.js.map",
    ]).pipe(dest("webroot/libs/bootstrap"));
}

function watchTask(cb) {
    browserSync.init({
        proxy: "shcc.localhost",
    });
    gulp.watch("src/scss/**/*.+(scss|sass)", scssTask);
    gulp.watch("webroot/**/*.+(php|html|css|js)").on("change",browserSync.reload);
    cb();
}

function defaultTask(cb) {
    watchTask();
    cb();
}

exports.bootstrap = bootstrapTask;
exports.scss = scssTask;
exports.highcharts = highchartsTask;
exports.jslibs = parallel(bootstrapTask, highchartsTask);
exports.watch = watchTask;
exports.default = defaultTask;
