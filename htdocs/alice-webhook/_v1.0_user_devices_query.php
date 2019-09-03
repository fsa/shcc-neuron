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
    foreach (json_decode($yandex_device->capabilities) as $capability=>$value) {
        switch ($capability) {
            case 'on_off':
                $devices[$id]->addCapability(new \Yandex\SmartHome\Capabilities\OnOffState($smarthome_device->getPower()));
                break;
            case 'color_hsv':
                $devices[$id]->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelState('hsv', $smarthome_device->getHSV()));
                $devices[$id]->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelState('temperature_k', $smarthome_device->getCT()));
                break;
            case 'color_rgb':
                $devices[$id]->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelState('rgb', $smarthome_device->getRGB()));
                $devices[$id]->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelState('temperature_k', $smarthome_device->getCT()));
                break;
            default:
                throw new \AppException('Ошибка в настройках устройства яндекс. Навык '.$capability.' не реализован.');
        }
    }
    $id++;
}
httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]);