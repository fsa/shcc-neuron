"use strict";

document.addEventListener('DOMContentLoaded', function () {
    let device_states = document.querySelectorAll('.device-state');
    device_states.forEach(item => {
        let device_name = item.getAttribute('device_name');
        setState(device_name, 'Связь не установлена');
    });
    device_states.forEach(item => {
        item.addEventListener('click', (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute('device_name');
                setState(device_name, 'Обновлено');
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
    if(state!=='') {
        let Data = new Date();
        let Hour = Data.getHours();
        let Minutes = Data.getMinutes();
        let Seconds = Data.getSeconds();
        state=Hour+':'+Minutes+':'+Seconds+' '+state;
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
        console.log(result);
    });
}
