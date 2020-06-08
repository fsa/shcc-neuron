"use strict";

document.addEventListener('DOMContentLoaded', function () {
    let device_states = document.querySelectorAll('.device-state');
    device_states.forEach(item => {
        let device_name = item.getAttribute('device_name');
        updateDeviceState(device_name);
    });
    device_states.forEach(item => {
        item.addEventListener('click', (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute('device_name');
                updateDeviceState(device_name);
            }
        });
    });
    // boolean
    document.querySelectorAll('.device-power').forEach(item => {
        item.addEventListener('change', (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute('device_name');
                sendCommand(device_name, {"action": e.target.getAttribute('device_action'), "value": e.target.checked});
            }
        });
    });
    //integer
    document.querySelectorAll('.device-bright, .device-ct').forEach(item => {
        item.addEventListener('change', (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute('device_name');
                sendCommand(device_name, {"action": e.target.getAttribute('device_action'), "value": e.target.value});
            }
        });
    });
}, false);

function setState(device_name, state) {
    if (state !== '') {
        let Data = new Date();
        state = Data.toLocaleTimeString() + ' ' + state;
    }
    document.querySelector('#' + device_name + '_state').innerHTML = state;
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
            setState(device_name, '');
        }
    });
}

function updateDeviceState(device_name) {
    fetch('/api/device/?name=' + device_name)
            .then(response => {
                if (response.status === 200) {
                    return response.json();
                }
                setState(device_name, 'Ошибка');
                console.log(response);
            }).then(result => {
        if (result.error) {
            setState(device_name, result.error);
        } else {
            for (var key in result) {
                let value = result[key];
                if (typeof value === 'boolean') {
                    document.querySelector('#' + device_name + '_' + key).checked = value;
                } else {
                    document.querySelector('#' + device_name + '_' + key).value = value;
                }
            }
            setState(device_name, '');
        }
    });
}
