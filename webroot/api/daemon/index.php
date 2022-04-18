<?php

require_once '../../../vendor/autoload.php';
App::initJson();
$host = App::getSettings('daemon-ip', '127.0.0.1');
if (!is_null($host)) {
    if (getenv('REMOTE_ADDR') != $host) {
        die('Wrong host');
    }
}
$request = file_get_contents('php://input');
$json = json_decode($request);
if (!$json) {
    App::response()->returnError(400, 'Неверный JSON');
}
if (!isset($json->module)) {
    App::response()->returnError(500, 'Неверное имя модуля');
}
$redis = new SmartHome\DeviceStorage;
$redis->init(\SmartHome\Devices::getAllDevicesEntity());
$modules = new SmartHome\Modules;
if (!$modules->isModuleExists($json->module)) {
    App::response()->json(['daemon' => null]);
}
if (!$modules->isDaemonActive($json->module)) {
    App::response()->json(['daemon' => false]);
}
$response = ['daemon' => true, 'class' => $modules->getDaemonClass($json->module), 'settings' => $modules->getDaemonSettings($json->module)];
$tz = getenv('TZ');
if ($tz) {
    $response['timezone'] = $tz;
}
App::response()->json($response);
