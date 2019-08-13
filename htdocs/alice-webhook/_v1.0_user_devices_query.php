<?php
if(!isset($request_id)) {die;}
#\Auth\Bearer::grantAccess();
#TODO: разгрести $request_content со списком устройств
$request=json_decode($request_content);
$id=0;
foreach ($request->devices as $device) {
    $devices[$id]=new \Yandex\SmartHome\DeviceState($device->id);
    $devices[$id]->addCapabilitie(new Yandex\SmartHome\Capabilities\onOffState('on', true));
    $id++;
}

httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]);