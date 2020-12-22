"use strict";
const sensors = new Set();

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[sensor]').forEach(item => {
        sensors.add(item.getAttribute('sensor'));
    });
    updatePage();
    setInterval(() => updatePage(), 30000);
});

function updatePage() {
    updateSensorsState();
    updateMessageLog('/api/tts/messages/', '#tts_message_log');
    updateMessageLog('/api/system/state/', '#system_state');
    let datetime = new Date();
    document.querySelector('#page_last_update').innerHTML=datetime.toLocaleString();
}

function updateSensorsState() {
    fetch('/api/sensors/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=utf-8'
        },
        body: JSON.stringify({'sensors': Array.from(sensors)})
    }).then(response => {
        if (response.status === 200) {
            return response.json();
        }
        alert('Ошибка при получении данных');
    }).then(result => {
        result.sensors.forEach(function (sensor) {
            document.querySelectorAll('[sensor="' + sensor.uid + '"]').forEach((item) => {
                setElementValue(item, sensor.value);
            });
            setLastUpdate(sensor.uid, '', sensor.ts * 1000);
        });
    });
}

function setLastUpdate(sensor, state, timestamp = 0) {
    let style = '';
    if (timestamp === 0) {
        let datetime = new Date();
        state = datetime.toLocaleString() + ' ' + state;
        style = '#dc3545';
    } else {
        let datetime = new Date(timestamp);
        state = datetime.toLocaleString() + ' ' + state;
        if (new Date() - datetime > 3600000) {
            style = '#ffc107';
        }
    }
    document.querySelectorAll('[sensor-lastupdate="' + sensor + '"]').forEach((item) => {
        setElementValue(item, state);
        item.style.color = style;
    });
}

function updateMessageLog(url, selector) {
    fetch(url
    ).then(response => {
        if (response.status === 200) {
            return response.json();
        }
        document.querySelector(selector).innerHTML='Ошибка';
    }).then(result => {
        let element=document.querySelector(selector);
        if(!element) {
            return;
        }
        if (result.error) {
            element.innerHTML=result.error;
        } else {
            element.innerHTML=result.join('<br>');
        }
    });
}

function setElementValue(e, value) {
    if (e.nodeName === 'INPUT') {
        if (e.type === 'checkbox') {
            e.checked = value;
        } else {
            e.value = value;
        }
    } else {
        e.innerHTML = value;
    }
}
