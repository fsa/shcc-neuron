<?php
if(!isset($request_id)) {die;}
#\Auth\Bearer::grantAccess();
#TODO: разгрести $request_content со списком устройств
$request=json_decode($request_content);
file_put_contents('query_'.date('Y_m_d').'.txt', print_r($request, true).PHP_EOL, FILE_APPEND | LOCK_EX);
$id=0;
foreach ($request->devices as $device) {
    /* @var $yeelight Yeelight\GenericDevice */
    $yeelight=\SmartHome\Devices::get($device->id);
    $yeelight->refreshState();
    $power=$yeelight->getPowerValue();
    $devices[$id]=new \Yandex\SmartHome\DeviceState($device->id);
    $devices[$id]->addCapabilitie(new Yandex\SmartHome\Capabilities\onOffState('on', $power));
    $id++;
}
file_put_contents('query_'.date('Y_m_d').'.txt', print_r(json_encode([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]), true).PHP_EOL, FILE_APPEND | LOCK_EX);
httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]);