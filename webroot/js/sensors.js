"use strict";
let request;

document.addEventListener('DOMContentLoaded', function () {
    const sensors = new Set();
    const text_messages = new Set();
    const request_body={};
    document.querySelectorAll('[sensor]').forEach(item => {
        sensors.add(item.getAttribute('sensor'));
    });
    document.querySelectorAll('[messages]').forEach(item => {
        text_messages.add(item.getAttribute('messages'));
    });
    if(sensors.size) {
        request_body.sensors=Array.from(sensors);
    }
    if(text_messages.size) {
        request_body.messages=Array.from(text_messages);
    }
    request=JSON.stringify(request_body);
    updatePage();
    setInterval(() => updatePage(), 30000);
});

function updatePage() {
    fetch('/api/', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=utf-8'
        },
        body: request
    }).then(response => {
        if (response.status === 200) {
            return response.json();
        }
        alert('Ошибка при получении данных');
    }).then(result => {
        if(result.sensors) {
            updateSensors(result.sensors);
        }
        if(result.messages) {
            updateTextMessages(result.messages);
        }
        let datetime = new Date();
        document.querySelector('#page_last_update').innerHTML=datetime.toLocaleString();
    });
}

function updateSensors(sensors) {
    sensors.forEach(function (sensor) {
        document.querySelectorAll('[sensor="' + sensor.uid + '"]').forEach((item) => {
            setElementValue(item, sensor.value);
        });
        setLastUpdate(sensor.uid, '', sensor.ts * 1000);
    });
}

function updateTextMessages(messages) {
    messages.forEach(function (message) {
        document.querySelector('[messages="' + message.name + '"]').innerHTML=message.content.join('<br>');
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
