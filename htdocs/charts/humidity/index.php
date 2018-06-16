<?php

require_once '../../common.php';
HTML::addHeader('<script src="/libs/jquery/jquery.min.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/highcharts.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
HTML::showPageHeader('Относительная влажность');
?>
<script>
var date=new Date();
date.setDate(date.getDate()-2);
var series = [{
        name: 'Комната',
        params: {place: 2, measure: 2, from: date.toJSON()}
        },{
        name: 'Кухня',
        params: {place: 3, measure: 2, from: date.toJSON()}
        }];
var title = 'Относительная влажность';
var units = '%';
var period = 'За период с по';
var seriesOptions=[], seriesCounter = 0;

function createChart() {
    Highcharts.setOptions({
        lang: {
            loading: 'Загрузка...',
            months: ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'],
            weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
            shortMonths: ['Янв', 'Фев', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сент', 'Окт', 'Нояб', 'Дек'],
            exportButtonTitle: "Экспорт",
            printButtonTitle: "Печать",
            rangeSelectorFrom: "С",
            rangeSelectorZoom: "Период",
            rangeSelectorTo: "По",
            downloadPNG: 'Скачать PNG',
            downloadJPEG: 'Скачать JPEG',
            downloadPDF: 'Скачать PDF',
            downloadSVG: 'Скачать SVG',
            printChart: 'Напечатать график'
        },
        time: {
            timezoneOffset: - 420
        }
    });
    Highcharts.chart('chart', {
        chart: {
        type: 'line'
        },
        credits: {
            href: 'http://tavda.net/',
                    text: 'Tavda.net'
        },
        title: {
        text: title
        },
        subtitle: {
        text: period
        },
        xAxis: {
            type: 'datetime',
            crosshair: {
            enabled: true,
                    color: '#00572b'
            }
        },
        yAxis: {
            title: {
                text: title
            },
            labels: {
                formatter: function () {
                    return this.value + ' ' + units;
                }
            }
        },
        tooltip: {
        split: true,
                distance: 30,
                pointFormat: '{series.name}<br><b>' + title + ': {point.y:,.2f} ' + units + '</b>',
                xDateFormat: '%d.%m.%Y %H:%M:%S'
        },
        plotOptions: {
            area: {
                marker: {
                    enabled: false,
                        symbol: 'circle',
                        radius: 2,
                        states: {
                        hover: {
                        enabled: true
                        }
                    }
                }
            }
        },
        series: seriesOptions
    });
};

$.each(series, function (i, serie) {
    $.getJSON('/api/meter_history/', serie.params, function (data) {
        seriesOptions[seriesCounter] = {
            name: serie.name,
            data: data
        };
        seriesCounter += 1;
        if (seriesCounter === series.length) {
            createChart();
        }
    });
});
</script>
<div style="width: 100%; height: 500px;" id="chart"></div>
<?php

HTML::showPageFooter();
