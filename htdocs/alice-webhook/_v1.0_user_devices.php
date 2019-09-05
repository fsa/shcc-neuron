<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/get-devices-docpage/
 */
if(!isset($request_id)) {die;}
\Auth\Bearer::grantAccess();
$user_id='anonymous';
$yandex_devices=Yandex\SmartHome\Devices::get(Auth\Bearer::getUserId());
$devices=[];
while ($device=$yandex_devices->fetch()) {
    $smarthome_device=\SmartHome\Devices::get($device->unique_name);
    $entity=new \Yandex\SmartHome\DeviceInfo($device->uid);
    $entity->setName($device->name);
    $entity->setRoom($device->room);
    $entity->setType($device->type);
    $entity->setDescription($device->description);
    foreach (json_decode($device->capabilities) as $capability=> $value) {
        switch ($capability) {
            case 'on_off':
                $entity->addCapability(new \Yandex\SmartHome\Capabilities\OnOff());
                break;
            case 'color_temperature':
                $result=new \Yandex\SmartHome\Capabilities\ColorModel();
                $result->setTemperatureK(isset($value->min)?$value->min:2000, isset($value->max)?$value->max:9000, isset($value->precision)?$value->precision:400);
                $entity->addCapability($result);
                break;
            case 'color_rgb';
                $result=new \Yandex\SmartHome\Capabilities\ColorModel();
                if (isset($value->min) and isset($value->max)) {
                    $result->setTemperatureK($value->min, $value->max, isset($value->precision)?$value->precision:400);
                }
                $result->setRGBModel();
                $entity->addCapability($result);
                break;
            case 'color_hsv';
                $result=new \Yandex\SmartHome\Capabilities\ColorModel();
                if (isset($value->min) and isset($value->max)) {
                    $result->setTemperatureK($value->min, $value->max, isset($value->precision)?$value->precision:400);
                }
                $result->setHSVModel();
                $entity->addCapability($result);
                break;
            case 'brightness':
                $result=new \Yandex\SmartHome\Capabilities\Range('brightness');
                $result->setUnit(isset($value->unit)?$value->unit:'percent');                                     $result->setRange(isset($value->min)?$value->min:1, isset($value->max)?$value->max:100, isset($value->precision)?$value->precision:1);                
                $entity->addCapability($result);
                break;
            default:
                throw new \AppException('Ошибка в настройках устройства яндекс. Навык '.$capability.' не реализован.');
        }
    }
    $entity->setDeviceManufacturer('phpmd');
    $devices[]=$entity;
}
httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'user_id'=>$user_id,
        'devices'=>$devices
    ]
]);
