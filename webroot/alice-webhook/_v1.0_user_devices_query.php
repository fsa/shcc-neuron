<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/post-devices-query-docpage/
 */
if(!isset($request_id)) {die;}
Auth\Server::grantAccess();
$request=json_decode($request_content);
$devices=[];
foreach ($request->devices as $device) {
    $yandex_device=Yandex\SmartHome\Devices::getByUid($device->id, Auth\Server::getUserId());
    if(!$yandex_device) {
        continue;
    }
    $smarthome_device=\SmartHome\Devices::get($yandex_device->unique_name);
    $smarthome_device->refreshState();
    $entity=new \Yandex\SmartHome\DeviceState($device->id);
    foreach (json_decode($yandex_device->capabilities) as $capability=>$value) {
        switch ($capability) {
            case 'on_off':
                $entity->addCapability(new \Yandex\SmartHome\Capabilities\OnOffState($smarthome_device->getPower()));
                break;
            case 'color_hsv':
                $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelState('hsv', $smarthome_device->getHSV()));
                $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelState('temperature_k', $smarthome_device->getCT()));
                break;
            case 'color_rgb':
                $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelState('rgb', $smarthome_device->getRGB()));
                $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelState('temperature_k', $smarthome_device->getCT()));
                break;
            case 'brightness':
/* @var $smarthome_device \SmartHome\Device\Capability\BrightnessInterface */
                $entity->addCapability(new Yandex\SmartHome\Capabilities\RangeState('brightness', $smarthome_device->getBrightness()));
                break;
            default:
                throw new \AppException('Ошибка в настройках устройства яндекс. Навык '.$capability.' не реализован.');
        }
    }
    $devices[]=$entity;
}
httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]);