<?php
require_once '../../common.php';
DB::query('SET @@session.group_concat_max_len = 10000;');
$stmt=DB::prepare('SELECT place_id, GROUP_CONCAT(CONCAT("[",UNIX_TIMESTAMP(timestamp),"000, ",value,"]") ORDER BY timestamp) AS data FROM meter_history WHERE measure_id=? GROUP BY place_id');
$stmt->execute([1]);
$data=$stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$stmt->closeCursor();
$stmt=DB::prepare('SELECT id,name FROM places WHERE id IN ('.join(',',array_keys($data)).')');
$stmt->execute();
$places=$stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$stmt->closeCursor();
HTML::addHeader('<script src="/libs/jquery/jquery.min.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/highcharts.js"></script>');
HTML::addHeader('<script src="/libs/highcharts/exporting.js"></script>');
HTML::showPageHeader('Температура');
?>
<script>
var series = [{
        name: '<?=$places[2]?>',
        data: [<?=$data[2]?>]
    },{
        name: '<?=$places[3]?>',
        data: [<?=$data[3]?>]
    }];
var title = 'Температура';
var units = '\u00B0C';
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
