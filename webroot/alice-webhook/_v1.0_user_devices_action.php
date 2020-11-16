<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/post-action-docpage/
 */
if (!isset($request_id)) {die;}
\Auth\Server::grantAccess();
$request=json_decode($request_content);
$devices=[];
foreach ($request->payload->devices as $device) {
    $yandex_device=Yandex\SmartHome\Devices::getByUid($device->id, Auth\Server::getUserId());
    if (!$yandex_device) {
        continue;
    }
    $smarthome_device=\SmartHome\Devices::get($yandex_device->unique_name);
    $entity=new \Yandex\SmartHome\DeviceResult($device->id);
    foreach ($device->capabilities as $capability) {
        switch ($capability->type) {
            case 'devices.capabilities.on_off':
                $power=$capability->state->value;
                $smarthome_device->setPower($power);
                $entity->addCapability(new \Yandex\SmartHome\Capabilities\OnOffResult());
                break;
            case 'devices.capabilities.color_setting':
                switch ($capability->state->instance) {
                    case 'temperature_k':
                        $smarthome_device->setCT($capability->state->value);
                        $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelResult('temperature_k'));
                        break;
                    case 'rgb':
                        $smarthome_device->setRGB($capability->state->value);
                        $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelResult('rgb'));
                        break;
                    case 'hsv':
                        $smarthome_device->setHSV($capability->state->value->h, $capability->state->value->s, $capability->state->value->v);
                        $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelResult('hsv'));
                        break;
                }
                break;
            case 'devices.capabilities.mode':
                #TODO: action
                $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelResult('UNDER_CONSTRUCTION', 'Construction in progress...'));
                break;
            case 'devices.capabilities.range':
                switch ($capability->state->instance) {
                    case 'brightness':
/* @var $smarthome_device \SmartHome\Device\Capability\BrightnessInterface */
                        $smarthome_device->setBrightness($capability->state->value);
                        break;
                    case 'temperature':
                        #TODO: action
                        $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelResult('UNDER_CONSTRUCTION', 'Construction in progress...'));
                        break;
                    case 'volume':
                        #TODO: action
                        $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelResult('UNDER_CONSTRUCTION', 'Construction in progress...'));
                        break;
                    case 'channel':
                        #TODO: action
                        $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelResult('UNDER_CONSTRUCTION', 'Construction in progress...'));
                        break;
                    default:
                        $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelResult('INVALID_RANGE', 'Unsupported range instance'));
                }
                break;
            case 'devices.capabilities.toggle':
                #TODO: action
                $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelResult('UNDER_CONSTRUCTION', 'Construction in progress...'));
                break;
            default:
                $entity->addCapability(new \Yandex\SmartHome\Capabilities\ColorModelResult('INVALID_ACTION', 'Unsupported action'));
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
