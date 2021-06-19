"use strict";
let request;

document.addEventListener('DOMContentLoaded', function () {
    const sensors = new Set();
    const text_messages = new Set();
    const device_names = new Set();
    const request_body={};
    document.querySelectorAll('[sensor]').forEach(item => {
        sensors.add(item.getAttribute('sensor'));
    });
    document.querySelectorAll('[messages]').forEach(item => {
        text_messages.add(item.getAttribute('messages'));
    });
    document.querySelectorAll('[device_name]').forEach(item => {
        device_names.add(item.getAttribute('device_name'));
    });
    if(sensors.size) {
        request_body.sensors=Array.from(sensors);
    }
    if(text_messages.size) {
        request_body.messages=Array.from(text_messages);
    }
    if(device_names.size) {
        request_body.devices=Array.from(device_names);
    }
    request=JSON.stringify(request_body);
    document.querySelectorAll('.action-state').forEach(item => {
        item.addEventListener('click', (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute('device_name');
                updateDeviceState(device_name);
            }
        });
    });
    document.querySelectorAll('.action-boolean').forEach(item => {
        item.addEventListener('change', (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute('device_name');
                sendCommand(device_name, {"action": e.target.getAttribute('device_property'), "value": e.target.checked});
            }
        });
    });
    document.querySelectorAll('.action-integer').forEach(item => {
        item.addEventListener('change', (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute('device_name');
                sendCommand(device_name, {"action": e.target.getAttribute('device_property'), "value": e.target.value});
            }
        });
    });
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
        return {error: 'Ошибка при получении данных с сервера, код ответа ' + response.status};
    }).then(result => {
        if(result.error) {
            result={messages: [{name: "state", content: [result.error]}]};
        }
        if(result.sensors) {
            updateSensors(result.sensors);
        }
        if(result.messages) {
            updateTextMessages(result.messages);
        }
        if(result.devices) {
            updateDevices(result.devices);
        }
        updateLastUpdateTime();
    });
}

function updateLastUpdateTime() {
    let datetime = new Date();
    document.querySelector('#page_last_update').innerHTML=datetime.toLocaleString();
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

function updateDevices(devices) {
    devices.forEach(function (device) {
        for (var key in device.state) {
            document.querySelectorAll('[device_name="' + device.name + '"][device_property="' + key + '"]').forEach((item) => {
                setElementValue(item, device.state[key]);
            });
        }
        setState(device.name, device.last_update > 0 ? '': 'Устройство ' + device.name + ' не найдено', device.last_update*1000);
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

function sendCommand(device_name, command) {
    fetch('/api/device/?name=' + device_name, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json;charset=utf-8'
        },
        body: JSON.stringify(command)
    }).then(response => {
        if (response.status === 200) {
            return response.json();
        }
        setState(device_name, 'Ошибка');
        console.log(response);
    }).then(result => {
        if (result.error) {
            setState(device_name, result.error);
        } else {
            setState(device_name, '', Date.now());
        }
    });
}

function setState(device_name, state, timestamp = 0) {
    let style = '';
    if (timestamp === 0) {
        let datetime = new Date();
        state = datetime.toLocaleString() + ' ' + state;
        style = '#dc3545';
    } else {
        let datetime=new Date(timestamp);
        state = datetime.toLocaleString() + ' ' + state;
        if (new Date()-datetime>3600000) {
            style = '#ffc107';
        }
    }
    document.querySelectorAll('[device_name="' + device_name + '"][device_property="last_update"]').forEach((item) => {
        setElementValue(item, state);
        item.style.color=style;
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
        let round=e.getAttribute('round');
        e.innerHTML = (round===null)?value:value.toFixed(round);
    }
}
