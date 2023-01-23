<?php

if (!isset($action)) {
    die;
}
$device = new FSA\SmartHome\Entity\Device;
$device->uid = filter_input(INPUT_POST, 'uid');
$device->description = filter_input(INPUT_POST, 'description');
$device->plugin = filter_input(INPUT_POST, 'plugin');
$device->hwid = filter_input(INPUT_POST, 'hwid');
$device->class = filter_input(INPUT_POST, 'class');
$properties = filter_input(INPUT_POST, 'properties', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
if (is_array($properties)) {
    $device->properties = $properties;
}
switch ($action) {
    case 'insert':
        App::deviceDatabase()->set(null, $device);
        updateDevice($device->uid, $device->properties);
        $response->storeNotification('Устройство добавлено');
        $response->redirection('../');
        break;
    case 'update':
        App::deviceDatabase()->set(filter_input(INPUT_POST, 'old_uid'), $device);
        updateDevice($device->uid, $device->properties);
        $response->storeNotification('Данные об устройстве обновлены');
        $response->redirection('../');
        break;
}
App::response()->returnError(400, 'Неизвестное действие');

function updateDevice($uid, $properties) {
    if (!$properties) {
        return;
    }
    $device = App::getDevice($uid);
    $device->init($device->getHwid(), $properties);
    App::setDevice($uid, $device);
}
