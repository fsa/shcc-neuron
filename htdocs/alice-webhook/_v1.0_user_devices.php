<?php
if(!isset($request_id)) {die;}
#\Auth\Bearer::grantAccess();
$user_id='FSA';
$devices[0]=new \Yandex\SmartHome\DeviceInfo('yeelight_bslamp');
$devices[0]->setName('Светильник');
$devices[0]->setRoom('Спальня');
$devices[0]->setType('light');
#$devices[0]->setDescription('Mi Bedside lamp');
$on_off=new Yandex\SmartHome\Capabilities\onOff();
$devices[0]->addCapabilitie($on_off);
$devices[0]->setDeviceManufacturer('phpmd');

#$devices[1]=new \Yandex\SmartHome\DeviceInfo('test-1231');
#$devices[1]->setName('Люстра');
#$devices[1]->setRoom('Спальня');
#$devices[1]->setType('light');
#$devices[1]->setDescription('Mi Bedside lamp');
#$devices[1]->addCapabilitie($on_off);
#$devices[1]->setDeviceManufacturer('phpmd');

httpResponse::json([
    'request_id'=>$request_id,
    'payload'=>[
        'user_id'=>$user_id,
        'devices'=>$devices
    ]
]);