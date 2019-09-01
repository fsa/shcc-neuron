<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/post-action-docpage/
 */
if(!isset($request_id)) {die;}
\Auth\Bearer::grantAccess();
$request=json_decode($request_content);
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
httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]);