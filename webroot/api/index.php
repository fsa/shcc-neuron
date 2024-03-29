<?php

require_once '../../vendor/autoload.php';
App::initJson();
App::session()->grantAccess(['control']);
$request = file_get_contents('php://input');
$json = json_decode($request);
$response = [];
if (isset($json->sensors)) {
    $response['sensors'] = [];
    foreach ($json->sensors as $uid) {
        $sensor = App::sensorStorage()->get($uid);
        if (is_null($sensor)) {
            continue;
        }
        $sensor->uid = $uid;
        $response['sensors'][] = $sensor;
    }
}
if (isset($json->devices)) {
    $response['devices'] = [];
    foreach ($json->devices as $device_name) {
        $device = App::getDevice($device_name);
        if ($device) {
            $response['devices'][] = ['name' => $device_name, 'state' => $device->getState(), 'last_update' => $device->getLastUpdate()];
        } else {
            $response['devices'][] = ['name' => $device_name, 'state' => null, 'last_update' => null];
        }
    }
}
if (isset($json->messages)) {
    $response['messages'] = [];
    foreach ($json->messages as $messages) {
        switch ($messages) {
            case 'state':
                $response['messages'][] = ['name' => 'state', 'content' => getState()];
                break;
            case 'tts':
                $response['messages'][] = ['name' => 'tts', 'content' => App::tts()->getLogMessages()];
                break;
        }
    }
}
if (count($response) > 0) {
    App::response()->json($response);
} else {
    App::response()->returnError(404);
}

function getState()
{
    $state = [];
    if (App::getVar('System:NightMode')) {
        $state[] = 'Включен ночной режим.';
    }
    if (App::getVar('System:SecurityMode')) {
        $state[] = 'Включен режим охраны.';
    }
    if (sizeof($state) == 0) {
        $state[] = 'Система работает в обычном режиме.';
    }
    return $state;
}
