<?php

if (!isset($action)) {
    die;
}
$device=new \SmartHome\Entity\Device;
$device->id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
$device->unique_name=filter_input(INPUT_POST,'unique_name');
$device->module_id=filter_input(INPUT_POST,'module_id',FILTER_VALIDATE_INT);
$device->uid=filter_input(INPUT_POST,'uid');
$device->description=filter_input(INPUT_POST,'description');
$device->classname=filter_input(INPUT_POST,'classname');
$init=filter_input(INPUT_POST,'init',FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
if(is_array($init)) {
    $device->init_data=json_encode($init);
}
$device->place_id=filter_input(INPUT_POST,'place_id',FILTER_VALIDATE_INT);
if(!$device->place_id) {
    $device->place_id=null;
}
$device->disabled=filter_input(INPUT_POST,'disabled')==!false;
$devices=new \SmartHome\Devices;
$devices->setDevice($device);
if($device->id) {
    $devices->update();
    HTML::showNotification('Обновление устройства','Данные об устройстве обновлены','../');
} else {
    $devices->insert();
    HTML::showNotification('Добавление устройства','Устройство добавлено','../');
}
