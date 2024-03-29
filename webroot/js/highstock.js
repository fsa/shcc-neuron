`use strict`;
const colors = ['#527779', '#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'];

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
    },
    colors: colors
});

document.addEventListener('DOMContentLoaded', initPage);
addEventListener('hashchange', () => {
    location.reload();
});

function initPage() {
    let list_element = document.querySelector('#charts_list');
    let chart_id = Number.parseInt(location.hash.substring(1));
    if (!chart_id) {
        chart_id = 0;
    }
    configs.forEach((config, id) => {
        list_element.innerHTML += `<li class="nav-item"><a class="nav-link" sensor_id="${id}" href="#${id}">${config.name}</a></li>`;
    });
    document.querySelectorAll(`[sensor_id]`).forEach(item => {
        if (item.getAttribute('sensor_id') == chart_id) {
            item.classList.add('active');
        } else {
            item.classList.remove('active');
        }
    });
    if (configs[chart_id]) {
        createChart(configs[chart_id]);
    } else {
        alert('Неверный адрес страницы');
    }
}

function HtmlDecode(s) {
    const el = document.createElement("div");
    el.innerHTML = s;
    return el.innerText;
}

function createChart(config) {
    let units = HtmlDecode(config.unit);
    chart = Highcharts.stockChart('chart', {
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
            }, {
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
        xAxis: {
            type: 'datetime',
            crosshair: {
                enabled: true,
                color: '#00572b'
            },
            ordinal: false,
            max: new Date().getTime()
        },
        yAxis: {
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
            line: {
                marker: {
                    enabled: true,
                    symbol: 'circle',
                    radius: 3
                }
            },
            series: {
                showInNavigator: false
            }
        },
        legend: {
            enabled: true
        },
        navigator: {
            enabled: false
        }
    });
    config.sensors.forEach((item, i) => {
        fetch(`/api/history/?uid=${item}`)
            .then(response => {
                if (response.status === 200) {
                    return response.json();
                }
            }).then(sensor => {
                if (sensor.data && sensor.data.length > 0) {
                    sensor.data = sensor.data.map((item) => [
                        item[0] * 1000,
                        item[1],
                    ]);
                    sensor.color = colors[i];
                    chart.addSeries(sensor);
                }
            });
    });
};
