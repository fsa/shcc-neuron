"use strict";

document.addEventListener('DOMContentLoaded', function () {
    updateDevices();
    document.querySelectorAll('.action-state').forEach(item => {
        item.addEventListener('click', (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute('device_name');
                updateDeviceState(device_name);
            }
        });
    });
    // boolean
    document.querySelectorAll('.action-boolean').forEach(item => {
        item.addEventListener('change', (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute('device_name');
                sendCommand(device_name, {"action": e.target.getAttribute('device_action'), "value": e.target.checked});
            }
        });
    });
    //integer
    document.querySelectorAll('.action-integer').forEach(item => {
        item.addEventListener('change', (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute('device_name');
                sendCommand(device_name, {"action": e.target.getAttribute('device_action'), "value": e.target.value});
            }
        });
    });
}, false);

function setState(device_name, state, timestamp = 0) {
    let style = false;
    if (timestamp === 0) {
        let Data = new Date();
        state = Data.toLocaleString() + ' ' + state;
        style = true;
    } else {
        let datetime=new Date(timestamp * 1000);
        if (new Date()-datetime>3600000) {
            style = true;
        }
        state = datetime.toLocaleString() + ' ' + state;
    }
    document.querySelectorAll('[device_name="' + device_name + '"][device_property="last_update"]').forEach((item) => {
        setElementValue(item, state);
        if (style === true) {
            item.classList.add('text-danger');
        } else {
            item.classList.remove('text-danger');
        }
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
            setState(device_name, '', new Date().toLocaleString());
        }
    });
}

function updateDevices() {
    let device_names = new Set();
    document.querySelectorAll('[device_name]').forEach(item => {
        device_names.add(item.getAttribute('device_name'));
    });
    device_names.forEach(item => {
        updateDeviceState(item);
    });
}

function updateDeviceState(device_name) {
    fetch('/api/device/?name=' + device_name)
            .then(response => {
                if (response.status === 200) {
                    return response.json();
                }
                setState(device_name, 'Ошибка', true);
                console.log(response);
            }).then(result => {
        if (result.error) {
            setState(device_name, result.error);
        } else {
            for (var key in result.properties) {
                document.querySelectorAll('[device_name="' + device_name + '"][device_property="' + key + '"]').forEach((item) => {
                    setElementValue(item, result.properties[key]);
                });
            }
            setState(device_name, result.last_update > 0 ? '': 'Нет данных', result.last_update);
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

setInterval(() => updateDevices(), 30000);