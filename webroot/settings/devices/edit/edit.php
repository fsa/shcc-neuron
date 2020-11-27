<?php

if (!isset($action)) {
    die;
}
$device=new \SmartHome\Entity\Device;
$device->uid=filter_input(INPUT_POST,'uid');
$device->hwid=filter_input(INPUT_POST,'hwid');
$device->description=filter_input(INPUT_POST,'description');
$entity=new stdClass();
$entity->classname=filter_input(INPUT_POST,'classname');
$init=filter_input(INPUT_POST,'init',FILTER_DEFAULT,FILTER_REQUIRE_ARRAY);
if(is_array($init)) {
    $entity->properties=$init;
}
$device->entity=json_encode($entity);
$device->place_id=filter_input(INPUT_POST,'place_id',FILTER_VALIDATE_INT);
if(!$device->place_id) {
    $device->place_id=null;
}
$devices=new \SmartHome\Devices;
$devices->setDevice($device);
switch ($action) {
    case 'insert':
        $devices->insert();
        httpResponse::storeNotification('Устройство добавлено');
        httpResponse::redirection('../');
        break;
    case 'update':
        $devices->update();
        httpResponse::storeNotification('Данные об устройстве обновлены');
        httpResponse::redirection('../');
        break;
}
httpResponse::showError('Неизвестное действие');