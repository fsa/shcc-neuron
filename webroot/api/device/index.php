<?php

require_once '../../../vendor/autoload.php';
App::initJson();
App::session()->grantAccess(['control']);
$device_name = filter_input(INPUT_GET, 'name');
if (!$device_name) {
    App::response()->returnError(400);
}
if (file_exists($device_name . '.php')) {
    chdir('../../../custom/command/');
    require_once '../functions.php';
    try {
        $result = require_once $device_name . '.php';
        App::response()->json($result);
    } catch (AppException $ex) {
        App::response()->returnError(500, $ex->getMessage());
    }
}
$device = SmartHome\Devices::get($device_name);
if (is_null($device)) {
    App::response()->returnError(400);
}
$request = json_decode(file_get_contents('php://input'));
if ($request) {
    try {
        $value = $request->value;
        switch ($request->action) {
            case 'power':
                $device->setPower($value);
                break;
            case 'bright':
                $device->setBrightness($value);
                break;
            case 'ct':
                $device->setCT($value);
                break;
        }
    } catch (AppException $ex) {
        App::response()->returnError(500, $ex->getMessage());
    }
}
App::response()->json(['properties' => $device->getState(), 'last_update' => $device->getLastUpdate()]);
