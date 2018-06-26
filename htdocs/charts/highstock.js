var seriesOptions=[], seriesCounter = 0;
var chart;

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
        rangeSelectorTo: "по",
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

function createChart() {
    chart=Highcharts.stockChart('chart', {
        rangeSelector: {
            selected: 0,
            buttons: [{
                type: 'day',
                count: 1,
                text: '1д'
            }, {
                type: 'day',
                count: 3,
                text: '3д'
            }, {
                type: 'day',
                count: 6,
                text: '6д'
            },{
                type: 'month',
                count: 1,
                text: '1м'
            }, {
                type: 'year',
                count: 1,
                text: '1г'
            }, {
                type: 'all',
                text: 'Всё'
            }]
        },
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
        xAxis: {
            type: 'datetime',
            crosshair: {
                enabled: true,
                        color: '#00572b'
            },
            events: {
                afterSetExtremes: afterSetExtremes
            },
            ordinal: false
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
                pointFormat: '{series.name}<br><b>{point.y:,.2f} ' + units + '</b>',
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


function afterSetExtremes(e) {

    chart.showLoading();
    seriesCounter = 0;
    $.each(series, function (i, serie) {
        params=serie.params;
        params.from=new Date(e.min).toJSON();
        params.to=new Date(e.max).toJSON();
        $.getJSON('/api/meter_history/', params, function (data) {
            seriesOptions[seriesCounter] = {
                name: serie.name,
                data: data
            };
            
            seriesCounter += 1;
            if (seriesCounter === series.length) {
                chart.hideLoading();
            }
        });
    });
}
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

