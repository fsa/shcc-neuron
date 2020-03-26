<?php

require_once '../../common.php';
httpResponse::setModeJson();
Auth\Session::grantAccess(['control']);
chdir('../../../custom/command/');
require_once '../functions.php';
$device_name=filter_input(INPUT_GET,'device_name');
$filename=$device_name.'.php';
if($device_name and file_exists($filename)) {
    try {
        $result=require_once $filename;
        httpResponse::json($result);
    } catch (AppException $ex) {
        httpResponse::showError($ex->getMessage());
    }
}
httpResponse::showError('Устройство не настроено');