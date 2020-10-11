`use strict`;
let units,unit;
let seriesOptions=[], seriesCounter = 0;
let chart;

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
    colors: ['#527779', '#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4']
});

function HtmlDecode(s) {
  const el = document.createElement("div");
  el.innerHTML = s;
  return el.innerText;
}

function createChart(unit) {
    let title=HtmlDecode(unit.name);
    let units=HtmlDecode(unit.unit);
    let minimal=Number(unit.minimal);
    let maximal=Number(unit.maximal);
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
            },
            softMin: minimal,
            softMax: maximal
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
                showInNavigator: true
            }
        },
        legend: {
            enabled: true
        },
        series: seriesOptions
    });
};


function afterSetExtremes(e) {
    chart.showLoading();
    seriesCounter = 0;
    for (let i in unit.places) {
        place=unit.places[i];
        let from=new Date(e.min).toJSON();
        let to=new Date(e.max).toJSON();
        fetch(`/api/meter/history/?place=${place.id}&unit=${unit.id}&from=${from}&to=${to}`)
        .then(response => {
            if (response.status === 200) {
                return response.json();
            }
        }).then(data => {
            seriesOptions[seriesCounter] = {
                name: place.name || 'Неизвестно',
                data: data
            };
            seriesCounter += 1;
            if (seriesCounter === unit.places.length) {
                chart.hideLoading();
            }
        });
    }
}

let chart_id = parseInt(location.hash.substr(1),10);
if(isNaN(chart_id)) {
    chart_id=1;
}
fetch('/api/meter/units/')
.then(response => {
    if (response.status === 200) {
        return response.json();
    }
}).then(units => {
    let list_element=document.querySelector('#charts_list');
    for (let id in units) {
        let unit=units[id];
        let node=document.createElement('li');
        node.classList.add("nav-item");
        node.innerHTML=`<a class="nav-link${id==chart_id?' active':''}" href="./?unit=${id}#${id}" onClick="startChart(${id})">${unit.name}</a>`;
        list_element.appendChild(node);
    }
});
//startChart(chart_id);
fetch('/api/meter/places/?unit='+chart_id)
.then(response => {
    if (response.status === 200) {
        return response.json();
    }
}).then(mp => {
    unit=mp;
    for (let i in mp.places) {
        let place=mp.places[i];
        fetch(`/api/meter/history/?place=${place.id}&unit=${mp.id}`)
        .then(response => {
            if (response.status === 200) {
                return response.json();
            }
        }).then(data => {
            seriesOptions[seriesCounter] = {
                name: place.name || 'Неизвестно',
                data: data
            };
            seriesCounter += 1;
            if (seriesCounter === mp.places.length) {
                createChart(mp);
            }
        });
    }
});
