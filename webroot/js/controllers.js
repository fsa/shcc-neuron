"use strict";

document.addEventListener('DOMContentLoaded', function () {
    updatePage();
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
        let datetime = new Date();
        state = datetime.toLocaleString() + ' ' + state;
        style = true;
    } else {
        let datetime=new Date(timestamp);
        state = datetime.toLocaleString() + ' ' + state;
        if (new Date()-datetime>3600000) {
            style = true;
        }
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
            setState(device_name, '', Date.now());
        }
    });
}

function updatePage() {
    let device_names = new Set();
    document.querySelectorAll('[device_name]').forEach(item => {
        device_names.add(item.getAttribute('device_name'));
    });
    device_names.forEach(item => {
        updateDeviceState(item);
    });
    updateMessageLog('/api/tts/messages/', '#tts_message_log');
    updateMessageLog('/api/system/state/', '#system_state');
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
            for (var key in result.properties) {
                document.querySelectorAll('[device_name="' + device_name + '"][device_property="' + key + '"]').forEach((item) => {
                    setElementValue(item, result.properties[key]);
                });
            }
            setState(device_name, result.last_update > 0 ? '': 'Нет данных', result.last_update*1000);
        }
    });
}

function updateMessageLog(url, selector) {
    fetch(url)
            .then(response => {
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

setInterval(() => updatePage(), 30000);