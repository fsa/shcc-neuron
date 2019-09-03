<?php
/**
 * https://yandex.ru/dev/dialogs/alice/doc/smart-home/reference/post-action-docpage/
 */
if(!isset($request_id)) {die;}
\Auth\Bearer::grantAccess();
$request=json_decode($request_content);
$id=0;

try {
    
foreach ($request->payload->devices as $device) {
    $yandex_device=Yandex\SmartHome\Devices::getByUid($device->id, Auth\Bearer::getUserId());
    if(!$yandex_device) {
        continue;
    }
    $smarthome_device=\SmartHome\Devices::get($yandex_device->unique_name);
    $devices[$id]=new \Yandex\SmartHome\DeviceResult($device->id);
    foreach ($device->capabilities as $capability) {
        switch ($capability->type) {
            case 'devices.capabilities.on_off':
                $power=$capability->state->value;
                $smarthome_device->setPower($power);
                $devices[$id]->addCapability(Yandex\SmartHome\Devices::getCapabilityResult($smarthome_device, $capability->type));
                break;
            case 'devices.capabilities.color_setting':
                switch ($capability->state->instance) {
                    case 'temperature_k':
                        $smarthome_device->setCT($capability->state->value);
                        $devices[$id]->addCapability(Yandex\SmartHome\Devices::getCapabilityResult($smarthome_device, $capability->type));
                        break;
                    case 'rgb':
                        $smarthome_device->setRGB($capability->state->value);
                        $devices[$id]->addCapability(Yandex\SmartHome\Devices::getCapabilityResult($smarthome_device, $capability->type));
                        break;
                }
                break;
            default:
                $devices[$id]->addCapability(Yandex\SmartHome\Devices::getCapabilityResult($smarthome_device, $capability->type),'INVALID_ACTION','Invalid action');
        }
    }
    $id++;
}
} catch (Exception $ex) {
file_put_contents('json_'.date('Y_m_d').'.txt', 'Ans: '.json_encode([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices,
        'error'=>$ex->getMessage()
    ]], JSON_UNESCAPED_UNICODE).PHP_EOL, FILE_APPEND | LOCK_EX);

}
httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'devices'=>$devices
    ]
]);