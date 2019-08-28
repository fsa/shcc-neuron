<?php

require_once '../../common.php';
Auth\Internal::grantAccess(['control']);
$dir='../../../custom/command/';
$device_name=filter_input(INPUT_GET,'device_name');
$filename=$device_name.'.php';
if($device_name and file_exists($dir.$filename)) {
    chdir($dir);
    $result=require_once $filename;
    httpResponse::json($result);
}
httpResponse::json(['error'=>'Устройство не настроено']);