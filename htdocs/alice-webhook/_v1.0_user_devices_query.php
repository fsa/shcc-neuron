<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/post-devices-query-docpage/
 */
if(!isset($request_id)) {die;}
Auth\Bearer::grantAccess();
$request=json_decode($request_content);
$id=0;
foreach ($request->devices as $device) {
    $yandex_device=Yandex\SmartHome\Devices::getByUid($device->id, Auth\Bearer::getUserId());
    if(!$yandex_device) {
        continue;
    }
    $smarthome_device=\SmartHome\Devices::get($yandex_device->unique_name);
    $smarthome_device->refreshState();
    $devices[$id]=new \Yandex\SmartHome\DeviceState($device->id);
    foreach (json_decode($yandex_device->capabilities) as $capability_name) {
        $devices[$id]->addCapability(Yandex\SmartHome\Devices::getCapabilityState($smarthome_device, $capability_name));
    }
    $id++;
}
httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]);