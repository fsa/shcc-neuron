<?php

use FSA\SmartHome\Capability\{PowerInterface, BrightnessInterface, ColorTInterface};

require_once '../../../vendor/autoload.php';
$response = App::initJson();
App::session()->grantAccess(['control']);
$device_name = filter_input(INPUT_GET, 'name');
if (!$device_name) {
    $response->returnError(400);
}
if (file_exists($device_name . '.php')) {
    chdir('../../../custom/command/');
    require_once '../functions.php';
    try {
        $result = require_once $device_name . '.php';
        $response->json($result);
    } catch (AppException $ex) {
        $response->returnError(500, $ex->getMessage());
    }
}
$device = App::getDevice($device_name);
if (is_null($device)) {
    $response->returnError(400);
}
$request = json_decode(file_get_contents('php://input'));
if ($request) {
    try {
        $value = $request->value;
        switch ($request->action) {
            case 'power':
                if ($device instanceof PowerInterface) {
                    $device->setPower($value);
                }
                break;
            case 'bright':
                if ($device instanceof BrightnessInterface) {
                    $device->setBrightness($value);
                }
                break;
            case 'ct':
                if ($device instanceof ColorTInterface) {
                    $device->setCT($value);
                }
                break;
        }
    } catch (AppException $ex) {
        $response->returnError(500, $ex->getMessage());
    }
}
$response->json(['properties' => $device->getState(), 'last_update' => $device->getLastUpdate()]);
