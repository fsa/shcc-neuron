<?php

if(!isset($request_id)) {die;}
#\Auth\Bearer::grantAccess();
#TODO: разгрести $request_content со списком устройств
$request=json_decode($request_content);
$id=0;
foreach ($request->payload->devices as $device) {
    $devices[$id]=new \Yandex\SmartHome\DeviceState($device->id);
    $state=new Yandex\SmartHome\Capabilities\onOffState('on', true);
    $state->setActionResultDone();
    $devices[$id]->addCapabilitie($state);
    $id++;
}

file_put_contents('json_'.date('Y_m_d').'.txt', json_encode($devices).PHP_EOL, FILE_APPEND | LOCK_EX);

httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]);