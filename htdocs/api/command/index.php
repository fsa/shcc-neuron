<?php

require_once '../../common.php';
httpResponse::setJsonExceptionHanler();
Auth\Session::grantAccess(['control']);
chdir('../../../custom/command/');
require_once '../functions.php';
$device_name=filter_input(INPUT_GET,'device_name');
$filename=$device_name.'.php';
if($device_name and file_exists($filename)) {
    $result=require_once $filename;
    httpResponse::json($result);
}
httpResponse::json(['error'=>'Устройство не настроено']);