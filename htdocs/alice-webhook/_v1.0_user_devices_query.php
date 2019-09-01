<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/post-devices-query-docpage/
 */
if(!isset($request_id)) {die;}
\Auth\Bearer::grantAccess();
$request=json_decode($request_content);
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
httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]);