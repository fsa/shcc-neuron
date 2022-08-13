"use strict";
let request;

document.addEventListener("DOMContentLoaded", function () {
    const sensors = new Set();
    const text_messages = new Set();
    const device_names = new Set();
    const request_body = {};
    document.querySelectorAll("[sensor]").forEach((item) => {
        sensors.add(item.getAttribute("sensor"));
    });
    document.querySelectorAll("[messages]").forEach((item) => {
        text_messages.add(item.getAttribute("messages"));
    });
    document.querySelectorAll("[device_name]").forEach((item) => {
        device_names.add(item.getAttribute("device_name"));
    });
    if (sensors.size) {
        request_body.sensors = Array.from(sensors);
    }
    if (text_messages.size) {
        request_body.messages = Array.from(text_messages);
    }
    if (device_names.size) {
        request_body.devices = Array.from(device_names);
    }
    request = JSON.stringify(request_body);
    document.querySelectorAll(".action-state").forEach((item) => {
        item.addEventListener("click", (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute("device_name");
                updateDeviceState(device_name);
            }
        });
    });
    document.querySelectorAll(".action-boolean").forEach((item) => {
        item.addEventListener("change", (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute("device_name");
                sendCommand(device_name, {
                    action: e.target.getAttribute("device_property"),
                    value: e.target.checked,
                });
            }
        });
    });
    document.querySelectorAll(".action-integer").forEach((item) => {
        item.addEventListener("change", (e) => {
            if (e.target) {
                let device_name = e.target.getAttribute("device_name");
                sendCommand(device_name, {
                    action: e.target.getAttribute("device_property"),
                    value: e.target.value,
                });
            }
        });
    });
    updatePage();
    setInterval(() => updatePage(), 30000);
});

function updatePage() {
    fetch("/api/", {
        method: "POST",
        headers: {
            "Content-Type": "application/json;charset=utf-8",
        },
        body: request,
    })
        .then((response) => {
            if (response.status === 200) {
                return response.json();
            }
            return {
                error:
                    "Сервер некорректно ответил на запрос, код состояния HTTP " +
                    response.status +
                    ".",
            };
        })
        .then((result) => {
            if (result.error) {
                showRequestError(result.error);
            } else {
                if (result.sensors) {
                    updateSensors(result.sensors);
                }
                if (result.messages) {
                    updateTextMessages(result.messages);
                }
                if (result.devices) {
                    updateDevices(result.devices);
                }
            }
            updateLastUpdateTime();
        })
        .catch((ex) => {
            showRequestError("Ошибка при получении данных с сервера: " + ex);
        });
}

function showRequestError(text) {
    document.querySelector('[messages="state"]').innerHTML = text;
}

function updateLastUpdateTime() {
    let datetime = new Date();
    document.querySelector("#page_last_update").innerHTML =
        datetime.toLocaleString();
}

function updateSensors(sensors) {
    sensors.forEach(function (sensor) {
        document
            .querySelectorAll('[sensor="' + sensor.uid + '"]')
            .forEach((item) => {
                setElementValue(item, sensor.value);
            });
        setLastUpdateSensor(sensor.uid, "", sensor.ts * 1000);
    });
}

function updateTextMessages(messages) {
    messages.forEach(function (message) {
        let json,
            msg = [];
        message.content.forEach(function (row) {
            try {
                json = JSON.parse(row);
                var date = new Date(json.ts * 1000);
                var hours = date.getHours();
                var minutes = date.getMinutes();
                var formattedTime =
                    (hours < 10 ? "0" + hours : hours) +
                    ":" +
                    (minutes < 10 ? "0" + minutes : minutes);

                msg.push(formattedTime + " " + json.message);
            } catch (e) {
                msg.push(row);
            }
        });
        document.querySelector('[messages="' + message.name + '"]').innerHTML =
            msg.join("<br>");
    });
}

function updateDevices(devices) {
    devices.forEach(function (device) {
        for (var key in device.state) {
            document
                .querySelectorAll(
                    '[device_name="' +
                        device.name +
                        '"][device_property="' +
                        key +
                        '"]'
                )
                .forEach((item) => {
                    setElementValue(item, device.state[key]);
                });
        }
        setLastUpdateDevice(
            device.name,
            device.last_update > 0
                ? ""
                : "Устройство " + device.name + " недоступно",
            device.last_update * 1000
        );
    });
}

function setLastUpdateSensor(sensor, state, timestamp = 0) {
    setLastUpdateElements(
        document.querySelectorAll('[sensor-lastupdate="' + sensor + '"]'),
        state,
        timestamp
    );
}

function setLastUpdateDevice(device_name, state, timestamp = 0) {
    setLastUpdateElements(
        document.querySelectorAll(
            '[device_name="' + device_name + '"][device_property="last_update"]'
        ),
        state,
        timestamp
    );
}

function setLastUpdateElements(elements, state, timestamp = 0) {
    let style = "";
    if (timestamp === 0) {
        let datetime = new Date();
        state = formatDateTime(datetime) + " " + state;
        style = "#dc3545";
    } else {
        let datetime = new Date(timestamp);
        state = formatDateTime(datetime) + " " + state;
        if (new Date() - datetime > 3600000) {
            style = "#ffc107";
        }
    }
    elements.forEach((item) => {
        setElementValue(item, state);
        if (item.classList.contains("warning-colors")) {
            item.style.color = style;
        }
    });
}

function sendCommand(device_name, command) {
    fetch("/api/device/?name=" + device_name, {
        method: "POST",
        headers: {
            "Content-Type": "application/json;charset=utf-8",
        },
        body: JSON.stringify(command),
    })
        .then((response) => {
            if (response.status === 200) {
                return response.json();
            }
            setLastUpdateDevice(device_name, "Ошибка");
            console.log(response);
        })
        .then((result) => {
            if (result.error) {
                setLastUpdateDevice(device_name, result.error);
            } else {
                setLastUpdateDevice(device_name, "", Date.now());
            }
        });
}

function setElementValue(e, value) {
    if (e.nodeName === "INPUT") {
        if (e.type === "checkbox") {
            e.checked = value;
        } else {
            e.value = value;
        }
    } else {
        let round = e.getAttribute("round");
        e.innerHTML = round === null ? value : value.toFixed(round);
    }
}

function formatDateTime(datetime) {
    return datetime.toLocaleString();
}
