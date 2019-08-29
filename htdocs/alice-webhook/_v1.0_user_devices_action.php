<?php

if(!isset($request_id)) {die;}
#\Auth\Bearer::grantAccess();
#TODO: разгрести $request_content со списком устройств
$request=json_decode($request_content);
file_put_contents('action_'.date('Y_m_d').'.txt', print_r($request, true).PHP_EOL, FILE_APPEND | LOCK_EX);
$id=0;
foreach ($request->payload->devices as $device) {
    $yeelight=\SmartHome\Devices::get($device->id);
    $power=$device->capabilities[0]->state->value;
    $yeelight->setPower($power);
    $devices[$id]=new \Yandex\SmartHome\DeviceState($device->id);
    $state=new Yandex\SmartHome\Capabilities\onOffState('on', $power);
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