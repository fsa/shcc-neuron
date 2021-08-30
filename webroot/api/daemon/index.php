<?php

require_once '../../common.php';
$host=Settings::get('daemon-ip', '127.0.0.1');
if (!is_null($host)) {
    if (getenv('REMOTE_ADDR')!=$host) {
        die('Wrong host');
    }
}
$request=file_get_contents('php://input');
$json=json_decode($request);
if (!$json) {
    httpResponse::json(['error'=>'Неверный JSON']);
}
if (!isset($json->module)) {
    httpResponse::json(['error'=>'Неверное имя модуля']);
}
$redis=new SmartHome\DeviceStorage;
$redis->init(\SmartHome\Devices::getAllDevicesEntity());
$modules=new SmartHome\Modules;
if(!$modules->isModuleExists($json->module)) {
    httpResponse::json(['daemon'=>null]);
}
if(!$modules->isDaemonActive($json->module)) {
    httpResponse::json(['daemon'=>false]);
}
$response=['daemon'=>true, 'class'=>$modules->getDaemonClass($json->module), 'settings'=>$modules->getDaemonSettings($json->module)];
$tz=getenv('TZ');
if ($tz) {
    $response['timezone']=$tz;
}
httpResponse::json($response);
