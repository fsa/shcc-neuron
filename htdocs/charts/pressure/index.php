<?php
require_once '../../common.php';
$stmt=DB::prepare('SELECT CONCAT("[",UNIX_TIMESTAMP(timestamp),"000, ",value,"]") FROM meter_history WHERE place_id=2 AND measure_id=3');
$stmt->execute();
$data=$stmt->fetchAll(PDO::FETCH_COLUMN);
HTML::addHeader('<script src="/libs/jquery/jquery.min.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/highcharts.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
HTML::showPageHeader('Атмосферное давление');
?>
<script>
var series = [{
        name: 'Комната',
        data: [<?=join(',',$data)?>],
    }];
var title = 'Атмосферное давление';
var units = 'мм.рт.ст.';
var period = 'За период с по';
var chart;
$(document).ready(function () {
    Highcharts.setOptions({
        lang: {
            loading: 'Загрузка...',
            months: ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'],
            weekdays: ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'],
            shortMonths: ['Янв', 'Фев', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сент', 'Окт', 'Нояб', 'Дек'],
            exportButtonTitle: "Экспорт",
            printButtonTitle: "Печать",
            rangeSelectorFrom: "С",
            rangeSelectorTo: "По",
            rangeSelectorZoom: "Период",
            downloadPNG: 'Скачать PNG',
            downloadJPEG: 'Скачать JPEG',
            downloadPDF: 'Скачать PDF',
            downloadSVG: 'Скачать SVG',
            printChart: 'Напечатать график'
        },
        time: {
            timezoneOffset: -420
        }
    });
    window.chart = Highcharts.chart('chart', {
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
                    return this.value + ' '+ units;
                }
            }
        },
        tooltip: {
            split: true,
            distance: 30,
            pointFormat: '{series.name}<br><b>'+title+': {point.y:,.2f} '+units+'</b>',
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
        series: series
    });
});

</script>
<div style="width: 100%; height: 500px;" id="chart"></div>
<?php
HTML::showPageFooter();
