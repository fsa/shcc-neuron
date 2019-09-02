<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/get-devices-docpage/
 */
if(!isset($request_id)) {die;}
\Auth\Bearer::grantAccess();
$user_id='anonymous';
$yandex_devices=Yandex\SmartHome\Devices::get(Auth\Bearer::getUserId());
$devices=[];
$id=0;
while ($device=$yandex_devices->fetch()) {
    $smarthome_device=\SmartHome\Devices::get($device->unique_name);
    $devices[$id]=new \Yandex\SmartHome\DeviceInfo($device->uid);
    $devices[$id]->setName($device->name);
    $devices[$id]->setRoom($device->room);
    $devices[$id]->setType($device->type);
    $devices[$id]->setDescription($device->description);
    foreach (json_decode($device->capabilities) as $capability_name) {
        $devices[$id]->addCapability(Yandex\SmartHome\Devices::getCapability($smarthome_device, $capability_name));
    }
    $devices[$id]->setDeviceManufacturer('phpmd');    
}
httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'user_id'=>$user_id,
        'devices'=>$devices
    ]
]);