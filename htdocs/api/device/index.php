<?php

require_once '../../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess(['control']);
$device_name=filter_input(INPUT_GET, 'name');
if(!$device_name) {
    httpResponse::error(400);
}
if(file_exists($device_name.'.php')) {
    chdir('../../../custom/command/');
    require_once '../functions.php';
    try {
        $result=require_once $device_name.'.php';
        httpResponse::json($result);
    } catch (AppException $ex) {
        httpResponse::showError($ex->getMessage());
    }
}
//TODO: обработка данных устройства
$device=SmartHome\Devices::get($device_name);
if(is_null($device)) {
    httpResponse::error(404);
}
$request=json_decode(file_get_contents('php://input'));
if($request) {
    try {
        $value=$request->value;
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
        httpResponse::showError($ex->getMessage());
    }
}
httpResponse::json($device->getState());